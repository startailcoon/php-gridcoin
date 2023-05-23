<?php

require_once __DIR__ . "/../src/models/contract.php";

use phpGridcoin\Wallet;
use phpGridcoin\Models\ContractBeacon;

class SimpleTest extends \PHPUnit\Framework\TestCase {

    function testConnect() {
        Wallet::setNode('localhost', '25717', 'gridcoinrpc', 'bkw75QgtWAAQpnU0MHR4qIQIfAqXR7OxdvHPHI6xI4VMQKXXEkpfPo2dT');
        $this->assertIsBool(true);
    }

    public function testGetBlockCount() {
        $this->assertIsInt(Wallet::getblockcount());
    }

    public function testGetBlockHash() {
        $this->assertIsObject(Wallet::getblockbynumber(1));
        $this->assertObjectHasProperty("hash", Wallet::getblockbynumber(1));
    }

    public function testGetBlocksBatchWithTxData() {
        foreach(Wallet::getblocksbatch(1, 1, true) as $block) {
            $this->assertIsObject($block);
            $this->assertObjectHasProperty("hash", $block);
            $this->assertObjectHasProperty("tx", $block);
            $this->assertIsObject($block->tx[0]);
            $this->assertObjectHasProperty("txid", $block->tx[0]);
        }
    }

    public function testGetBlocksBatchWithoutTxData() {
        foreach(Wallet::getblocksbatch(1, 1, false) as $block) {
            $this->assertIsObject($block);
            $this->assertObjectHasProperty("hash", $block);
            $this->assertObjectHasProperty("tx", $block);
            $this->assertIsString($block->tx[0]);
        }
    }

    public function testGetTransaction() {
        $data = Wallet::gettransaction("945051eb9c4f6c56a7620d1112dab0122e41f2b17b58338e45ce58164c52e068");
        $this->assertIsObject($data);
        $this->assertObjectHasProperty("txid", $data);
    }

    public function testContractBeacon() {
        $data = Wallet::gettransaction("be7446e0fd091c5d45fc4d4e67dc7fc71526a7d5309add0c768cfdd5f1076fe0");
        
        /** @var ContractBeacon */
        $body = $data->contracts[0]->body;

        $this->assertIsObject($data);  
        $this->assertObjectHasProperty("cpid", $body);
    }

    public function testGetMempool() {

        $mempool = Wallet::getrawmempool();

        // This output can be an empty array, so we can't test for an object 
        // But we can test this to make sure the command works
        $this->assertIsArray($mempool);

        // If the mempool is not empty, we can test for the txid property
        if(!empty($mempool)) {
            foreach($mempool as $tx) {
                $this->assertIsObject($tx);
                $this->assertObjectHasProperty("txid", $tx);
            }
        }
    }

    // public function testBlock() {
    //     $this->connect();
    //     $block = Wallet::getblockbynumber(1);
    // }

}

?>
