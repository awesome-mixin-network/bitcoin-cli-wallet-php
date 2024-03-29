<?php
require __DIR__ . '/vendor/autoload.php';
use ExinOne\MixinSDK\MixinSDK;
use ExinOne\MixinSDK\Traits\MixinSDKTrait;
use Ramsey\Uuid\Uuid;
use MessagePack\MessagePack;

$mixinSdk_BotInstance = new MixinSDK(require './config.php');

const PIN             = "945689";
const MASTER_ID       = "37222956";
const EXIN_BOT        = "61103d28-3ac2-44a2-ae34-bd956070dab1";
const BTC_ASSET_ID    = "c6d0c728-2624-429b-8e0d-d9d19b6592fa";
const EOS_ASSET_ID    = "6cfe566e-4aad-470b-8c9a-2fd35b49c68d";
const USDT_ASSET_ID   = "815b0b1a-2764-3736-8faa-42d694fa620a";
const AMOUNT          = "0.0001";

// Change to the cold wallet address or third exchange wallet address  your own!
const BTC_WALLET_ADDR = "14T129GTbXXPGXXvZzVaNLRFPeHXD1C25C";
const EOS_THIRD_EXCHANGE_NAME
                      = "huobideposit";
const EOS_THIRD_EXCHANGE_TAG
                      = "1872050";
// Mixin Network support cryptocurrencies (2019-02-19)
// |EOS|6cfe566e-4aad-470b-8c9a-2fd35b49c68d
// |CNB|965e5c6e-434c-3fa9-b780-c50f43cd955c
// |BTC|c6d0c728-2624-429b-8e0d-d9d19b6592fa
// |ETC|2204c1ee-0ea2-4add-bb9a-b3719cfff93a
// |XRP|23dfb5a5-5d7b-48b6-905f-3970e3176e27
// |XEM|27921032-f73e-434e-955f-43d55672ee31
// |ETH|43d61dcd-e413-450d-80b8-101d5e903357
// |DASH|6472e7e3-75fd-48b6-b1dc-28d294ee1476
// |DOGE|6770a1e5-6086-44d5-b60f-545f9d9e8ffd
// |LTC|76c802a2-7c88-447f-a93e-c29c9e5dd9c8
// |SC|990c4c29-57e9-48f6-9819-7d986ea44985
// |ZEN|a2c5d22b-62a2-4c13-b3f0-013290dbac60
// |ZEC|c996abc9-d94e-4494-b1cf-2a3fd3ac5714
// |BCH|fd11b6e3-0b87-41f1-a41f-f0e9b49e5bf0

$msg  = "1 : Create Bitcoin Wallet and update PIN\n2 : Read Bitcoin balance & address \n3 : Read USDT balance & address \n";
$msg .= "4 : Read EOS balance & address\n";
$msg .= "qu: Read market price(USDT)\nqb: Read market price(BTC)\nb : Balance of  bot (USDT & BTC & EOS)\n";
$msg .= "s : Read Snapshots \ntb: Transfer 0.0001 BTC buy USDT\ntu: Transfer $1 USDT buy BTC\n";
$msg .= "we: Withdrawal EOS\nwb: Withdrawal BTC\n";
$msg .= "q : Exit \nMake your choose:";
while (true) {
  echo $msg;
  $line = readline("");
  if ($line != 'q') print("run...\n");
  if ($line == '1') {
    $wallet_info = $mixinSdk_BotInstance->Network()->createUser("My Bitcoin Wallet");
    print_r($wallet_info);

    $wallet_Config = array();
    $wallet_Config["private_key"] = $wallet_info["priKey"];
    $wallet_Config["pin_token"]   = $wallet_info["pin_token"];
    $wallet_Config["session_id"]  = $wallet_info["session_id"];
    $wallet_Config["client_id"]   = $wallet_info["user_id"];
    $wallet_Config["pin"]         = PIN;
    $mixinSdk_tomcat = new MixinSDK($wallet_Config);

    $pinInfo = $mixinSdk_tomcat->Pin()->updatePin('',PIN);
    print_r($pinInfo);
    $csvary = array($wallet_Config);
    $fp = fopen('mybitcoin_wallet.csv', 'a');
    foreach ($csvary as $fields) {
        fputcsv($fp, $fields);
    }
    fclose($fp);
  }
  if ($line == 'b') {
    $asset_info = $mixinSdk_BotInstance->Wallet()->readAsset(BTC_ASSET_ID);
    print_r("Bot Bitcoin wallet balance is :".$asset_info["balance"]."\n");
    $asset_info = $mixinSdk_BotInstance->Wallet()->readAsset(USDT_ASSET_ID);
    print_r("Bot USDT wallet balance is :".$asset_info["balance"]."\n");
    $asset_info = $mixinSdk_BotInstance->Wallet()->readAsset(EOS_ASSET_ID);
    print_r("Bot EOS wallet balance is :".$asset_info["balance"]."\n");
  }
  if ($line == '2') {
    if (($handle = fopen("mybitcoin_wallet.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $mixinSdk_eachAccountInstance = new MixinSDK(GenerateConfigByCSV($data));
      $asset_info = $mixinSdk_eachAccountInstance->Wallet()->readAsset(BTC_ASSET_ID);
      print_r("Bitcoin wallet address is :".$asset_info["public_key"]."\n");
      print_r("Bitcoin wallet balance is :".$asset_info["balance"]."\n");
      echo "You can deposit Bitcoin to your wallet this way!\n";
    }
      fclose($handle);
    } else print("Create user first\n");
  }
  if ($line == '3') {
    if (($handle = fopen("mybitcoin_wallet.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $mixinSdk_eachAccountInstance = new MixinSDK(GenerateConfigByCSV($data));
      $asset_info = $mixinSdk_eachAccountInstance->Wallet()->readAsset(USDT_ASSET_ID);
      print_r("USDT wallet address is :".$asset_info["public_key"]."\n");
      print_r("USDT wallet balance is :".$asset_info["balance"]."\n");
      echo "You can deposit USDT to your wallet this way!\n";
    }
      fclose($handle);
    } else print("Create user first\n");
  }
  if ($line == '4') {
    if (($handle = fopen("mybitcoin_wallet.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $mixinSdk_eachAccountInstance = new MixinSDK(GenerateConfigByCSV($data));
      $asset_info = $mixinSdk_eachAccountInstance->Wallet()->readAsset(EOS_ASSET_ID);
      print_r("EOS wallet name is :".$asset_info["account_name"]."\n address: ".$asset_info["account_tag"]."\n");
      print_r("EOS wallet balance is :".$asset_info["balance"]."\n");
      echo "You can deposit EOS to your wallet this way!\n";
    }
      fclose($handle);
    } else print("Create user first\n");
  }
  if ($line == 's') {
    $limit        = 20;
    // $offset       = '201-03-10T01:58:25.362528Z';

    $handle  = fopen("mybitcoin_wallet.csv", "r");
    $newUser = fgetcsv($handle, 1, ",");
    $mixinSdk_eachAccountInstance = new MixinSDK(GenerateConfigByCSV($newUser));

    $snapInfo = $mixinSdk_eachAccountInstance->Wallet()->readUserSnapshots($limit);
    fclose($handle);
    print_r($snapInfo);
    foreach ($snapInfo as  $record) {
      if ($record['amount'] > 0 and $record['memo'] != '') {
        echo "------------MEMO:-coin--exchange--------------" . PHP_EOL;
        echo "memo: " . $record['memo'] . PHP_EOL;
        echo "You Get Coins: ". $record['asset_id']. " " . $record['amount'] . PHP_EOL;
        $memoUnpack = MessagePack::unpack(base64_decode($record['memo']));
        $feeAssetID = Uuid::fromBytes($memoUnpack['FA'])->toString();
        $OrderID    = Uuid::fromBytes($memoUnpack['O'])->toString();
        if ($memoUnpack['C'] == 1000) {
          echo "Successful Exchange:". PHP_EOL;
          echo "Fee asset ID: " . $feeAssetID . " fee is :" . $memoUnpack['F'] . PHP_EOL;
          echo "Order ID: " . $OrderID . " Price is :" . $memoUnpack['P'] . PHP_EOL;
        } else print_r($memoUnpack);
        echo "--------------memo-record end---------------" . PHP_EOL;
      }
    }
  }
  if ($line == 'qu') {
    $marketInfo = getExchangeCoins(USDT_ASSET_ID);
    // echo $marketInfo;
  }
  if ($line == 'tb') {
    try {
      $handle = fopen("mybitcoin_wallet.csv", "r");
      $data = fgetcsv($handle, 1, ",");
      coinExchange(GenerateConfigByCSV($data),BTC_ASSET_ID,"0.0001",USDT_ASSET_ID);
      fclose($handle);
    } catch (ArithmeticError | Exception $e) {
       echo 'Message: ' .$e->getMessage() . PHP_EOL;
    }
  }
  if ($line == 'tu') {
    try {
      $handle = fopen("mybitcoin_wallet.csv", "r");
      $data = fgetcsv($handle, 1, ",");
      coinExchange(GenerateConfigByCSV($data),USDT_ASSET_ID,"1",BTC_ASSET_ID);
      fclose($handle);
    } catch (ArithmeticError | Exception $e) {
       echo 'Message: ' .$e->getMessage() . PHP_EOL;
    }
  }
  if ($line == 'we') {
    try {
      $handle = fopen("mybitcoin_wallet.csv", "r");
      $newUser = fgetcsv($handle, 1, ",");
      $mixinSdk_eachAccountInstance = new MixinSDK(GenerateConfigByCSV($newUser));
      $wdInfo = $mixinSdk_eachAccountInstance->Wallet()->createAddress(EOS_ASSET_ID,
                                                EOS_THIRD_EXCHANGE_TAG,
                                                $mixinSdk_eachAccountInstance->getConfig()['default']['pin'],
                                                EOS_THIRD_EXCHANGE_NAME,true);
      // coinExchange(GenerateConfigByCSV($data),USDT_ASSET_ID,"1",BTC_ASSET_ID);
      print("EOS winthdrawal fee is:".$wdInfo["fee"]."\n");
      $asset_info = $mixinSdk_eachAccountInstance->Wallet()->readAsset(EOS_ASSET_ID);
      $wdAmount = (float) $asset_info["balance"] - (float) $wdInfo["fee"];
      echo "EOS withdraw amount is: " . $wdAmount . PHP_EOL;
      if ( $wdAmount > 0 ) {
        echo "Are you deposit EOS " . floatval($wdAmount). " to " . EOS_THIRD_EXCHANGE_NAME . " " . EOS_THIRD_EXCHANGE_TAG . "(y/n)";
        $cmd = readline("");
        if ($cmd == 'y' ) {
          $wdInfo = $mixinSdk_eachAccountInstance->Wallet()->withdrawal($wdInfo["address_id"],
                                      floatval($wdAmount),
                                      $mixinSdk_eachAccountInstance->getConfig()['default']['pin'],
                                      "eos withdraw");
          print_r($wdInfo);
        }
      } else echo "Not Enough asset to withdraw!" . PHP_EOL.
      fclose($handle);
    } catch (ArithmeticError | Exception $e) {
       echo 'Message: ' .$e->getMessage() . PHP_EOL;
    }
  }
  if ($line == 'wb') {
    try {
      $handle = fopen("mybitcoin_wallet.csv", "r");
      $newUser = fgetcsv($handle, 1, ",");
      $mixinSdk_eachAccountInstance = new MixinSDK(GenerateConfigByCSV($newUser));
      $wdInfo = $mixinSdk_eachAccountInstance->Wallet()->createAddress(BTC_ASSET_ID,
                                                BTC_WALLET_ADDR,
                                                $mixinSdk_eachAccountInstance->getConfig()['default']['pin'],
                                                "BTC withdraw",false);
      // coinExchange(GenerateConfigByCSV($data),USDT_ASSET_ID,"1",BTC_ASSET_ID);
      print("Bitcoin winthdrawal fee is:".$wdInfo["fee"]."\n");
      $asset_info = $mixinSdk_eachAccountInstance->Wallet()->readAsset(BTC_ASSET_ID);
      echo "Balance is " .$asset_info["balance"] . PHP_EOL;
      $wdAmount = (float) $asset_info["balance"] - (float) $wdInfo["fee"];
      echo "Bitcoin withdraw amount is: " . $wdAmount . PHP_EOL;
      if ( $wdAmount > 0 ) {
        echo "Are you deposit Bitcoin " . floatval($wdAmount). " to '" . BTC_WALLET_ADDR  . "' (y/n)";
        $cmd = readline("");
        if ($cmd == 'y' ) {
          $wdInfo = $mixinSdk_eachAccountInstance->Wallet()->withdrawal($wdInfo["address_id"],
                                      floatval($wdAmount),
                                      $mixinSdk_eachAccountInstance->getConfig()['default']['pin'],
                                      "btc withdraw");
          print_r($wdInfo);
        }
      } else echo "Not Enough asset to withdraw!" . PHP_EOL.
      fclose($handle);
    } catch (ArithmeticError | Exception $e) {
       echo 'Message: ' .$e->getMessage() . PHP_EOL;
    }
  }
  if ($line == 'qb') {
    $marketInfo = getExchangeCoins(BTC_ASSET_ID);
    // echo $marketInfo;
  }
  if ($line == 'q') {
    exit();
  }
}

function GenerateConfigByCSV($data) :array {
  print("client id is:".$data[3]."\n");
  $newConfig = array();
  $newConfig["private_key"] = $data[0];
  $newConfig["pin_token"]   = $data[1];
  $newConfig["session_id"]  = $data[2];
  $newConfig["client_id"]   = $data[3];
  $newConfig["pin"]         = $data[4];
  return $newConfig;
}

function getExchangeCoins($base_coin) :string {
  $client = new GuzzleHttp\Client();
  $res = $client->request('GET', 'https://exinone.com/exincore/markets?base_asset='.$base_coin, [
      ]);
  $result = "";
  if ($res->getStatusCode() == "200") {
    // echo $res->getStatusCode() . PHP_EOL;
    $resInfo = json_decode($res->getBody(), true);
    echo "Asset ID | Asset Symbol | Price | Amount | Exchanges" . PHP_EOL;
    $result = "Asset ID | Asset Symbol | Price | Amount | Exchanges" . PHP_EOL;
    foreach ($resInfo["data"] as $key => $coinInfo) {
      echo ($coinInfo["exchange_asset"] ." ".$coinInfo["exchange_asset_symbol"]. "/". $coinInfo["base_asset_symbol"] .
            " ". $coinInfo["price"] ." ". $coinInfo["minimum_amount"] ."-". $coinInfo["maximum_amount"] . " ");
      $result .= $coinInfo["exchange_asset_symbol"]. "/". $coinInfo["base_asset_symbol"] .
                  " ". $coinInfo["price"] ." ". $coinInfo["minimum_amount"] ."-". $coinInfo["maximum_amount"] . " ";
      foreach ($coinInfo["exchanges"] as $key => $exchange) {
        echo $exchange . " ";
        $result .= $exchange . " ";
      }
      echo PHP_EOL;
      $result .= PHP_EOL;
    }
  }
  return $result;
}
function coinExchange ($config, $_assetID, $_amount, $_targetAssetID) {
  $memo = base64_encode(MessagePack::pack([
                       'A' => Uuid::fromString($_targetAssetID)->getBytes(),
                       ]));
  echo PHP_EOL . $memo . PHP_EOL;
  // $mixinSdk_eachAccountInstance= new MixinSDK(GenerateConfigByCSV($data));
  $mixinSdk_eachAccountInstance= new MixinSDK($config);
  $transInfo = $mixinSdk_eachAccountInstance->Wallet()->transfer($_assetID,
                EXIN_BOT,$config['pin'],$_amount,$memo);
  print_r($transInfo);
}
