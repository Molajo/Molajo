<?php
/**
 * Crypt Package:
 *
 * Tests for forcing errors and displaying messages/codes
 *
 * Will move into 'real' tests - need direction
 *
 */

// Import the platform.
require_once realpath(__DIR__) . '/tests/index.php';

/**
 *  SECTION 1
 *
 *  Test JCryptMessage Class
 */

// test 1: retrieve valid message
echo 'Message should be: "Invalid key of type. Expected simple." Message is: "'.JCryptMessage::get(300100).'"<br /><br />';

// test 2: retrieve default message for invalid code
echo 'Message should be: "Undefined Message". Message is: "'.JCryptMessage::get(999).'"<br /><br />';

// test 3: retrieve all messages
echo 'Should be an array<pre>: <br />';
var_dump(JCryptMessage::get(0));
echo '</pre><br />';

// test 4: retrieve valid code
echo 'Code should be "300100". Code is: "'.JCryptMessage::getCode('Invalid key of type. Expected simple.').'"<br /><br />';

// test 5: retrieve default code for invalid message
echo 'Code should be "300000". Code is: "'.JCryptMessage::getCode('This is not a real message.').'"<br /><br />';

/**
 * SECTION 2
 *
 * JCryptCipherMcrypt
 *
 * test 1 - requires PHP module mcrypt to not be loaded
 * How do you unload a php module?
 * Or, you can manually change if (!is_callable('mcrypt_encrypt')) to an incorrect value
 */
$message = 'Did not catch error';
$code = '0';
try {
    $cipher = new JCryptCipherBlowfish();
}
catch (Exception $e) {
    $message = $e->getMessage();
    $code = $e->getCode();
}
echo 'Note: this error can only be trapped if the PHP mcrypt module is not loaded. <br />';
echo 'Message should be "The mcrypt extension is not available." Message is ": ' . $message . '"<br />';
echo 'Code should be "300200". Code is: "' . $code . '"<br /><br />';

// test 2 - decrypt using mcrypt and simple key
$data = '';
$simple = new JCryptCipherSimple();
$blowfish = new JCryptCipherBlowfish();

$key = new JCryptKey('simple');
$key->private = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCUgkVF4mLxAUf80ZJPAJHXHoac';
$key->public = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCUgkVF4mLxAUf80ZJPAJHXHoac';

try {
    $results = $blowfish->decrypt($data, $key);
}
catch (Exception $e) {
    $message = $e->getMessage();
    $code = $e->getCode();
}
echo 'Message should be "Invalid JCryptKey used with Mcrypt decryption." Message is ": ' . $message . '"<br />';
echo 'Code should be "300300": ' . $code . '<br /><br />';

// test 3 - encrypt using mcrypt and simple key
$data = '';
$simple = new JCryptCipherSimple();
$blowfish = new JCryptCipherBlowfish();

$key = new JCryptKey('simple');
$key->private = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCUgkVF4mLxAUf80ZJPAJHXHoac';
$key->public = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCUgkVF4mLxAUf80ZJPAJHXHoac';

try {
    $results = $blowfish->encrypt($data, $key);
}
catch (Exception $e) {
    $message = $e->getMessage();
    $code = $e->getCode();
}
echo 'Message should be "Invalid JCryptKey used with Mcrypt encryption." Message is: "' . $message . '"<br />';
echo 'Code should be "300400": ' . $code . '<br /><br />';

/**
 * SECTION 3
 *
 * JCryptCipherSimple
 */
$message = 'Did not catch error';
$code = '0';

// test 1 - decrypt using simple and mcrypt key
$data = '';
$simple = new JCryptCipherSimple();
$blowfish = new JCryptCipherBlowfish();

$key = new JCryptKey('mcrypt');
$key->private = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCUgkVF4mLxAUf80ZJPAJHXHoac';
$key->public = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCUgkVF4mLxAUf80ZJPAJHXHoac';

try {
    $results = $simple->decrypt($data, $key);
}
catch (Exception $e) {
    $message = $e->getMessage();
    $code = $e->getCode();
}
echo 'Message should be "Invalid JCryptKey used with Simple decryption." Message is ": ' . $message . '"<br />';
echo 'Code should be "300500": ' . $code . '<br /><br />';

// test 1 - encrypt using simple and mcrypt key
$data = '';
$simple = new JCryptCipherSimple();
$blowfish = new JCryptCipherBlowfish();

// set key with Simple
$key = new JCryptKey('mcrypt');
$key->private = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCUgkVF4mLxAUf80ZJPAJHXHoac';
$key->public = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCUgkVF4mLxAUf80ZJPAJHXHoac';

// decrypt with blowfish
try {
    $results = $simple->encrypt($data, $key);
}
catch (Exception $e) {
    $message = $e->getMessage();
    $code = $e->getCode();
}
echo 'Message should be "Invalid JCryptKey used with Simple encryption." Message is: "' . $message . '"<br />';
echo 'Code should be "300600": ' . $code . '<br /><br />';


