# phpGridcoin

phpGridcoin is a RPC connector in PHP for the Gridcoin Research wallet. It connects to a local or remote wallet to fetch chain information.

**PLEASE BE AWARE**  that the current library is not working without the changes submitted to PR #209 on JSONMapper in https://github.com/cweiske/jsonmapper/pull/209



---
## Static Library
The code is written as a static Class, which means we set everything once, and no need to call more than once.


## Wallet Interactions

```
use phpGridcoin\Wallet;

// Optionally set a node, otherwise use public nodes set in the code
Wallet::addNode("localhost","25712","myRPCUser","myRPCPasswd");

// Optionally set allowPublicNodes to add fallbacks
Wallet::setAllowPublicNodes();

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
## Wallet Commands

Not all wallet commands have been added, see the list of [Wallet Commands](https://github.com/startailcoon/phpGridcoin/issues/1) here.
