<?php
// Credits
// Steve Gibson for the spec.
// First php version by Bob Somers (www.bobsomers.com)
// Modification to v3.1 by Orson Jones
// Improvements by Jaden Bjorn (mikeboers.com)
//	see: http://mikeboers.com/ppp.txt
// Everyone in the grc.thinktank newsgroup for hashing this idea to bits

// PPP in PHP (November 2, 2007)
// An implementation of the PPP CryptoSystem designed by Steve Gibson of GRC
// Full details and spec can be found at http://www.grc.com/ppp

// Implementation in PHP by Bob Somers (www.bobsomers.com)
// Tested on PHP version 5.2.3

// REQUIREMENTS:
// + PHP5, preferably 5.2.3 or higher (that's what I tested with)
// + The hash() function must support the SHA256 hash. You can check this with the hash_algos() function.
// + The bcmath extension for dealing with numbers of arbitrary size.
// + The mcrypt extension (built against libmcrypt > 2.4.x) for 128-bit Rijndael.

// generates a 256-bit sequence key by hashing the passed string with SHA256
// it isn't recommended to use this method, rather, you should generate a random
// key with GenerateRandomSequenceKey() instead
// returns the sequence key as a hex string
function GenerateSequenceKeyFromString($passphrase)
{
	return hash('sha256', $passphrase);
}

// generates a random 256-bit sequence key
// returns the sequence key as a hex string
function GenerateRandomSequenceKey()
{
	$randomness = get_loaded_extensions();
	$randomness[] = php_uname();
	$randomness[] = memory_get_usage();
	$randomness = implode(microtime(), $randomness);
	return hash('sha256', $randomness);
}

// pack the 128 bit number into a binary string (bcmath style number to binary)
function pack128( $num ) {
	$pack = '' ;
	while( $num ) {
		$pack .= chr( bcmod( $num, 256 ) ) ;
		$num = bcdiv( $num, 256 ) ;
	}
	return $pack ;
}

// unpack the 128 bit integer from a binary string (binary to bcmath style number)
function unpack128( $pack ) {
	$pack = str_split( strrev( $pack )) ;
	$num = '0' ;
	foreach( $pack as $char ) {
		$num = bcmul( $num, 256 ) ;
		$num = bcadd( $num, ord( $char )) ;
	}
	return $num ;
}

// calculate the number of characters in a crypto block for a character set of given length.
function blockchars( $length ) {
	return floor(128/(log($length, 2)));
}

// sort the character set
function sortchars($charset)
{
	$newchars = str_split($charset,1);
	sort($newchars);
	return implode('',$newchars);
}

// returns lotto numbers
function getlotto($key, $code)
{
	$sk = pack("H*", $key);
	$n_bits = pack128($code);
	$enc_bits = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $sk, $n_bits, MCRYPT_MODE_ECB, str_repeat( "\0", 16 ));
	$numdec = unpack128($enc_bits);
	$chars = "";
	for ($i = 0; $i < 5; $i++)
	{
		$chars .= bcadd(bcmod($numdec,56),1);
		if ($i < 4)
			$chars .= ", ";
		else
			$chars .= " / ";
		$numdec = bcdiv($numdec,56);
	}
	$chars .= bcadd(bcmod($numdec,46),1);
	return $chars;
}

// returns the nth number (Ex.: Port numbers, etc.)
function getnum($key, $code, $codemin, $codemax)
{
	$length = 1+$codemax-$codemin;
	$sk = pack("H*", $key);
	$n_bits = pack128($code);
	$enc_bits = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $sk, $n_bits, MCRYPT_MODE_ECB, str_repeat( "\0", 16 ));
	$numdec = unpack128($enc_bits);
	return bcadd(bcmod($numdec,$length),$codemin);
}

// returns the nth port and code - Based on idea from Hank Beaver in the GRC newsgroups
function getportcode($key, $code, $codemin, $codemax)
{
	$codes = array();
	$length = 1+$codemax-$codemin;
	$sk = pack("H*", $key);
	$n_bits = pack128($code);
	$enc_bits = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $sk, $n_bits, MCRYPT_MODE_ECB, str_repeat( "\0", 16 ));
	$numdec = unpack128($enc_bits);
	array_push($codes, bcadd(bcmod($numdec,$length),$codemin));
	$numdec = bcdiv($numdec,$length);
	$charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$length = strlen($charset);
	$codelength = 2;
	$chars = "";
	for ($i = 0; $i < $codelength; $i++)
	{
		$chars .= substr($charset,bcmod($numdec,$length),1);
		$numdec = bcdiv($numdec,$length);
	}
	array_push($codes, $chars);
	return $codes;
}

// returns the nth code
function getcode($key, $code, $charset, $codelength)
{
	$charset = sortchars($charset);
	$length = strlen($charset);
	$blockchars = blockchars($length);
	$sk = pack("H*", $key);
	$n_bits = pack128($code);
	$enc_bits = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $sk, $n_bits, MCRYPT_MODE_ECB, str_repeat( "\0", 16 ));
	$numdec = unpack128($enc_bits);
	$chars = "";
	for ($i = 0; $i < $codelength; $i++)
	{
		$chars .= substr($charset,bcmod($numdec,$length),1);
		$numdec = bcdiv($numdec,$length);
	}
	return $chars;
}

// return an array of the codes requested
function getcodes($key, $code, $num, $charset, $codelength)
{
	$codes = array();
	$first = $code;
	$last = bcadd($code, $num);
	$charset = sortchars($charset);
	$length = strlen($charset);
	$blockchars = blockchars($length);
	$sk = pack("H*", $key);
	for ($h = $first; bccomp($h,$last) < 0; $h = bcadd($h,1))
	{
		$n_bits = pack128($h);
		$enc_bits = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $sk, $n_bits, MCRYPT_MODE_ECB, str_repeat( "\0", 16 ));
		$numdec = unpack128($enc_bits);
		$chars = "";
		for ($i = 0; $i < $codelength; $i++)
		{
			$chars .= substr($charset,bcmod($numdec,$length),1);
			$numdec = bcdiv($numdec,$length);
		}
		array_push($codes, $chars);
	}
	return $codes;
}

// prints a card
function printcard($key,$charset,$codelength,$cardnum,$title="PPP Card")
{
	printf("%-30.30s%8s\n",$title,"[".$cardnum."]");
	$rows = 10;
	$cols = floor(35/($codelength+1));
	$total = $rows*$cols;
	echo "    ";
	for ($i = 0; $i < $cols; $i++)
	{
		echo str_pad(chr(ord("A")+$i), $codelength+1, " ", STR_PAD_BOTH);
	}
	echo "\n";
	$codes = getcodes($key,bcmul($cardnum,$total),$total,$charset,$codelength);
	for ($i = 0; $i < $total; $i++)
	{
		$code = $codes[$i];
		if ($i % $cols == 0)
			printf("%2s: ",ceil(($i+1)/$cols));
		if ($i % $cols < $cols-1)
			echo "$code ";
		else
			echo "$code\n";
	}
}

?>