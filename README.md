# phpGridcoin

phpGridcoin is a RPC connector in PHP for the Gridcoin Research wallet. It connects to a local or remote wallet to fetch chain information.

**PATCHED VERSION REQUIRED:** [Please see more here](#patched-version-required)

## PHP Library
The php library is written as a static Class, which means we set everything once, and no need to call more than once.

The library is located in `/src`.
* `/src/models/chain` has models for chain objects
* `/src/models/wallet` has models for wallet functions
* `/src/routes` has routes for wallet functions
* `/src/Wallet.php` has the master class for talking with the Gridcoin Wallet

To connect to a Gridcoin Wallet, all you need is the following code.

```
use CoonDesign\phpGridcoin\Wallet;

// Connect to your local wallet
Wallet::setNode("localhost","25712","myRPCUser","myRPCPasswd");

// Get Current Block Count
$currentBlockCount = Wallet::getblockcount();
```

If you need to connect to a second wallet, you can do so by adding it to a parameter, like so

```
$mySecondWallet = Wallet::setNode(...);
$mySecondWallet->getBlockCount();
```

Wallet connection paremeters are set in the wallet `gridcoinresearch.conf` file as follows
```
rpcuser=myRPCUser
rpcpassword=myRPCPasswd
rpcport=25712
```
---
## OpenAPI
phpGridcoin has a built in API built on OpenAPI located in `public/wallet/v1` that can be used for allowing remote access. 

See [an example Apache vhosts file](vhost.conf.example) that can be used.



## Wallet Commands

Not all wallet commands have been, or ever will be, added. The interesting commands will be to read the chain.

A list of all [Wallet Commands](https://github.com/startailcoon/phpGridcoin/issues/1) can be found here.

Some commands takes a very long time to perform for the wallet. To reduce waiting time, there are two solutions.

### Solution 1: Local Cache
A local cache can be used to store the results. An example of this is done on the `getburnreport` command and uses the class `WalletCache`, which uses Memcache/Redis to serve as a middlehand.

The initial request will still take time to performe. 

### Soltuion 2: Gridcoinstats API (not ready yet)
A faster way, if the data is available, is to use the API from Gridcoinstast.eu. 

This feature is no available yet.

## Development and Testing

When adding new features, please make sure to add a phpunit testcase to it. 

Testcases are in the `/test` folder. Functions start with `test`, example `testGetBlocks()`.

Make sure that `/vendor/bin/phpunit` does not fail when pushing new code.

## Patched version required

The current library is not working without a specific feature in netresearch/JSONMapper. This is described in [Issue 212](https://github.com/cweiske/jsonmapper/issues/212).

Until the original repo adds the feature need, it's required to use a patched version. 

This can be used by adding the following to the `composer.json` settings.

```
"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/startailcoon/jsonmapper"
        }
],
"require": {
    "netresearch/jsonmapper": "dev-master"
}
```