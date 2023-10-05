<?php

use CoonDesign\phpGridcoin\Wallet;
use CoonDesign\phpGridcoin\Routes\GetBestBlockHash;
use CoonDesign\phpGridcoin\Routes\GetBlockByNumber;
use CoonDesign\phpGridcoin\Routes\GetTransation;

class SimpleTest extends \PHPUnit\Framework\TestCase {

    function testConnect() {
        Wallet::setNode('localhost', '25717', 'gridcoinrpc', 'bkw75QgtWAAQpnU0MHR4qIQIfAqXR7OxdvHPHI6xI4VMQKXXEkpfPo2dT');
        $this->assertIsBool(true);
    }

    function testGetBestBlockHash() {
        $this->assertIsString(GetBestBlockHash::execute());
    }

    function testGetBlockByNumber() {
        $result = GetBlockByNumber::execute(1, true);
        $this->assertIsObject($result);
        $this->assertObjectHasProperty("hash", $result);
    }

    function testGetOutputTransaction() {
        $result = GetTransation::execute("cf115d5fcdf1c2f91bbc238e3e5c9616fab787932bb8d9400bcc9b27d020e631");
        $value = $result->getOutputValue();

        $this->assertIsFloat($value->getFloat());
        $this->assertIsInt($value->getInt());

        $this->assertEquals(29502869065, $value->getInt());
        $this->assertEquals(295.02869065, $value->getFloat());

        $this->assertIsArray($result->getOutputAddresses());
    }

    function testGetInputTransactionValue() {
        $result = GetTransation::execute("cf115d5fcdf1c2f91bbc238e3e5c9616fab787932bb8d9400bcc9b27d020e631");

        $txArray = [];

        foreach($result->vin as $vin) {
            if(isset($txArray[$vin->txid])) {
                continue;
            }

            $txArray[$vin->txid] = GetTransation::execute($vin->txid);
        }

        $value = $result->getInputValue($txArray);
        $this->assertIsFloat($value->getFloat());
        $this->assertIsInt($value->getInt());

        $this->assertEquals(29502969065, $value->getInt());
        $this->assertEquals(295.02969065, $value->getFloat());

        $addresses = $result->getInputAddresses($txArray);
        $this->assertIsArray($addresses);
    }

}

?>
