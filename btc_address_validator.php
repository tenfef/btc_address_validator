<?php

class btc_address_validator {
    
    public function validate($address)
    {        
        $addressversion = "00";
        $addr = $this->decode_base58($address);
        if (strlen($addr) != 50)
        {
          return false;
        }

        $version = substr($addr, 0, 2);
        if (hexdec($version) > hexdec($addressversion))
        {
          return false;
        }

        $check = substr($addr, 0, strlen($addr) - 8);
        $check = pack("H*", $check);
        $check = strtoupper(hash("sha256", hash("sha256", $check, true)));
        $check = substr($check, 0, 8);
        return $check == substr($addr, strlen($addr) - 8);
    }

    private function encode_hex($dec)
    {
        $hexchars = "0123456789ABCDEF";
        $return = "";

        while (bccomp($dec, 0) == 1)
        {
            $dv = (string) bcdiv($dec, "16", 0);
            $rem = (integer) bcmod($dec, "16");
            $dec = $dv;
            $return = $return . $hexchars[$rem];
        }
        return strrev($return);
   }

    /**
    * Convert a Base58-encoded integer into the equivalent hex string representation
    *
    * @param string $base58
    * @return string
    * @access private
    */
    private function decode_base58($base58)
    {
        $origbase58 = $base58;    
        $base58chars = "123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz"; 

        $return = "0";
        for ($i = 0; $i < strlen($base58); $i++)
        {
          $current = (string) strpos($base58chars, $base58[$i]);
          $return = (string) bcmul($return, "58", 0);
          $return = (string) bcadd($return, $current, 0);
        }

        $return = $this->encode_hex($return);

        //leading zeros
        for ($i = 0; $i < strlen($origbase58) && $origbase58[$i] == "1"; $i++)
        {
          $return = "00" . $return;
        }

        if (strlen($return) % 2 != 0)
        {
          $return = "0" . $return;
        }

        return $return;
    }
}