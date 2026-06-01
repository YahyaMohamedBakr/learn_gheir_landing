<?php

class SMTP {
    private $host;
    private $port;
    private $username;
    private $password;
    private $encryption;
    private $fromEmail;
    private $fromName;
    private $socket;
    private $timeout = 10;

    public function __construct($config) {
        $this->host = $config['host'] ?? '';
        $this->port = $config['port'] ?? 587;
        $this->username = $config['username'] ?? '';
        $this->password = $config['password'] ?? '';
        $this->encryption = $config['encryption'] ?? 'tls';
        $this->fromEmail = $config['from_email'] ?? '';
        $this->fromName = $config['from_name'] ?? 'Learn.Gheir';
    }

    public function send($to, $subject, $body) {
        try {
            $this->connect();
            $this->auth();
            $this->sendMail($to, $subject, $body);
            $this->quit();
            return ['success' => true, 'message' => 'تم إرسال البريد بنجاح'];
        } catch (Exception $e) {
            $this->close();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function connect() {
        $remote = $this->host . ':' . $this->port;
        $errno = 0;
        $errstr = '';

        $this->socket = @stream_socket_client($remote, $errno, $errstr, $this->timeout);
        if (!$this->socket) {
            throw new Exception("فشل الاتصال بالخادم: $errstr ($errno)");
        }
        stream_set_timeout($this->socket, $this->timeout);

        $this->readReply();

        $localHost = gethostname() ?: 'localhost';
        $this->sendCommand("EHLO $localHost");

        if ($this->encryption === 'tls') {
            $this->sendCommand("STARTTLS");
            $crypto = @stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            if (!$crypto) {
                throw new Exception("فشل تفعيل تشفير TLS");
            }
            $this->sendCommand("EHLO $localHost");
        }
    }

    private function auth() {
        if (empty($this->username)) return;
        $this->sendCommand("AUTH LOGIN");
        $this->sendCommand(base64_encode($this->username));
        $this->sendCommand(base64_encode($this->password));
    }

    private function sendMail($to, $subject, $body) {
        $fromHeader = $this->fromName ? "=?UTF-8?B?" . base64_encode($this->fromName) . "?= <{$this->fromEmail}>" : $this->fromEmail;

        $this->sendCommand("MAIL FROM:<{$this->fromEmail}>");
        $this->sendCommand("RCPT TO:<$to>");
        $this->sendCommand("DATA");

        $headers = [
            "From: $fromHeader",
            "To: <$to>",
            "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
            "Reply-To: {$this->fromEmail}",
            "MIME-Version: 1.0",
            "Content-Type: text/plain; charset=UTF-8",
            "Content-Transfer-Encoding: base64",
            "X-Mailer: PHP/SMTP",
            ""
        ];

        $message = implode("\r\n", $headers) . "\r\n" . chunk_split(base64_encode($body));
        $message .= "\r\n.";

        $this->sendCommandRaw($message);
    }

    private function quit() {
        $this->sendCommandRaw("QUIT\r\n");
        $this->close();
    }

    private function sendCommand($cmd) {
        $this->sendCommandRaw("$cmd\r\n");
        $this->readReply();
    }

    private function sendCommandRaw($data) {
        fwrite($this->socket, $data);
    }

    private function readReply() {
        $response = '';
        while (true) {
            $line = fgets($this->socket, 512);
            if ($line === false) {
                throw new Exception("فشل قراءة الرد من الخادم");
            }
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') break;
            if (feof($this->socket)) break;
        }

        $code = (int)substr($response, 0, 3);
        if ($code >= 400) {
            throw new Exception("خطأ SMTP ($code): " . trim($response));
        }

        return $response;
    }

    private function close() {
        if ($this->socket) {
            fclose($this->socket);
            $this->socket = null;
        }
    }

    public function __destruct() {
        $this->close();
    }
}
