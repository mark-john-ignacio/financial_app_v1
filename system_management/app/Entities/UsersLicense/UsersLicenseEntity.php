<?php

namespace App\Entities\UsersLicense;

use CodeIgniter\Entity\Entity;

class UsersLicenseEntity extends Entity
{
    protected $datamap = [
        'company' => 'compcode',
        'number'    => 'DecryptedNumber',
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
    private $key = "uPBIMitDgOHyg6tgdLcsDHRJuWJriwO8";
    private $cipher = "AES-128-CBC";

    public function encryptNumber($plaintext) {
        $ivlen = openssl_cipher_iv_length($this->cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $this->key, true);
        $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);
        return $ciphertext;
    }

    public function getDecryptedNumber() {
        $ciphertext = $this->attributes['value']; // Assuming 'value' is where the encrypted number is stored
        $c = base64_decode($ciphertext);
        $ivlen = openssl_cipher_iv_length($this->cipher);
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, 32); // Assuming SHA-256 HMAC
        $ciphertext_raw = substr($c, $ivlen + 32);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $this->key, true);
        if (hash_equals($hmac, $calcmac)) {
            return $original_plaintext;
        }
        return false; // or handle error
    }
}
