<?php

namespace Amp\Socket\Test;

use Amp\Socket;
use PHPUnit\Framework\TestCase;

class SocketTest extends TestCase
{
    public function testReadAndClose(): void
    {
        $data = "Testing\n";

        [$serverSock, $clientSock] = Socket\pair();

        \fwrite($serverSock, $data);
        \fclose($serverSock);

        $client = new Socket\ClientSocket($clientSock);

        while (($chunk = $client->read()) !== null) {
            $this->assertSame($data, $chunk);
        }
    }

    public function testSocketAddress(): void
    {
        @unlink(__DIR__ . '/socket.sock');

        $s = stream_socket_server('unix://' . __DIR__ . '/socket.sock');
        $c = stream_socket_client('unix://' . __DIR__ . '/socket.sock');

        $clientSocket = new Socket\ClientSocket($c);
        $serverSocket = new Socket\ServerSocket($s);

        $this->assertNotNull($clientSocket->getRemoteAddress());
        $this->assertSame(__DIR__ . '/socket.sock', $clientSocket->getLocalAddress());
        $this->assertSame($clientSocket->getRemoteAddress(), $clientSocket->getLocalAddress());
        $this->assertSame($serverSocket->getRemoteAddress(), $serverSocket->getLocalAddress());
        $this->assertSame($serverSocket->getRemoteAddress(), $clientSocket->getLocalAddress());
    }
}
