<?php


namespace TDD\test {
    require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

    use PHPUnit\Framework\TestCase;
    use Riseart\Api\Client;

    class ClientTest extends TestCase
    {
        protected $Client;

        public function setUp()
        {
            $this->Client = new Client();

        }

        public function tearDown(){
            unset($this->Client);
        }

        public function testInstance()
        {
            $this->assertInstanceOf(Client::class, $this->Client);
        }

        public function testInstanceWithAuthModuleApplication()
        {

            $this->assertInstanceOf(Client::class, new Client());
        }
    }
}
