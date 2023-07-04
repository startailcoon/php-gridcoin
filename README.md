# phpGridcoin

phpGridcoin is a RPC connector in PHP for the Gridcoin Research wallet. It connects to a local or remote wallet to fetch chain information.



---
## Static Library
The code is written as a static Class, which means we set everything once, and no need to call more than once.


## Wallet Interactions

```
use CoonDesign\phpGridcoin\Wallet;

// Connect to your local wallet
Wallet::setNode("localhost","25712","myRPCUser","myRPCPasswd");

// Get Current Block Count
$currentBlockCount = Wallet::getblockcount();
```

Wallet connection paremeters are set in the wallet `gridcoinresearch.conf` file as follows
```
rpcuser=myRPCUser
rpcpassword=myRPCPasswd
rpcport=25712
```
---
## Public API

TODO: Write how to use the API

## Wallet Commands

Not all wallet commands have been added, see the list of [Wallet Commands](https://github.com/startailcoon/phpGridcoin/issues/1) here.

---
## Development and Testing

When adding new features, please make sure to add a phpunit testcase to it. 

Testcases are in the `/test` folder. Functions start with `test`, example `testGetBlocks()`.

Make sure that `/vendor/bin/phpunit` does not fail when pushing new code.

## Please note
The current library is not working without the changes submitted to PR #209 on JSONMapper in https://github.com/cweiske/jsonmapper/pull/209
Until these changes are merged, this repo and any that relies on this one requires the following code to the `composer.json` settings

```
"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/startailcoon/jsonmapper"
        }
],
"require": {
    "netresearch/jsonmapper": "dev-classMapFix"
}
```