<?php

class Arabic
{
    public static function reshape($str)
    {
        if (!$str) {
            return $str;
        }

        $e = new self;
        return $e->utf8Glyphs($str);
    }

    protected function utf8Glyphs($str)
    {
        $str = (string) $str;
        
        if (!preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]/u', $str)) {
            return $str;
        }

        // Split UTF-8 string into array of characters
        $tokens = preg_split('/(\s+)/u', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
        $processedTokens = [];

        foreach ($tokens as $token) {
            if ($token === '') continue;
            
            // Check if token contains Arabic
            if (preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]/u', $token)) {
                // Split token into runs of Arabic and non-Arabic
                $subTokens = preg_split('/([\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]+)/u', $token, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                $processedSubTokens = [];
                
                foreach ($subTokens as $subToken) {
                    if (preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]/u', $subToken)) {
                        // Arabic sub-token: reshape and reverse
                        $processedSubTokens[] = $this->reshapeArabicSubToken($subToken);
                    } else {
                        // Non-Arabic sub-token: mirror brackets, do not reverse characters
                        $processedSubTokens[] = $this->mirrorBrackets($subToken);
                    }
                }
                
                // Since the token contains Arabic, we reverse the sequence of its sub-tokens
                $processedTokens[] = implode('', array_reverse($processedSubTokens));
            } else {
                // Pure LTR token: keep as is
                $processedTokens[] = $token;
            }
        }

        // Reverse the order of all tokens to render RTL in LTR flow
        return implode('', array_reverse($processedTokens));
    }

    protected function reshapeArabicSubToken($subToken)
    {
        $subChars = preg_split('//u', $subToken, -1, PREG_SPLIT_NO_EMPTY);
        $len = count($subChars);
        $reshapedSub = [];
        
        for ($j = 0; $j < $len; $j++) {
            $char = $subChars[$j];
            
            // Lam-Alef Ligature Check
            if ($char === mb_chr(0x0644) && $j < $len - 1) {
                $nextChar = $subChars[$j + 1];
                $nextOrd = mb_ord($nextChar);
                $ligatureCode = null;
                
                if ($nextOrd === 0x0622) { // ALEF MADDA
                    $ligatureCode = [0xFEF5, 0xFEF6];
                } elseif ($nextOrd === 0x0623) { // ALEF HAMZA ABOVE
                    $ligatureCode = [0xFEF7, 0xFEF8];
                } elseif ($nextOrd === 0x0625) { // ALEF HAMZA BELOW
                    $ligatureCode = [0xFEF9, 0xFEFA];
                } elseif ($nextOrd === 0x0627) { // ALEF
                    $ligatureCode = [0xFEFB, 0xFEFC];
                }
                
                if ($ligatureCode !== null) {
                    $prev = $j > 0 ? $subChars[$j - 1] : null;
                    $connectsPrev = ($prev && $this->isArabic($prev) && $this->connectsNext($prev));
                    
                    $formOrd = $connectsPrev ? $ligatureCode[1] : $ligatureCode[0];
                    $reshapedSub[] = mb_chr($formOrd);
                    $j++; // Skip next Alef
                    continue;
                }
            }
            
            // Standard reshaping
            $prev = $j > 0 ? $subChars[$j - 1] : null;
            $next = $j < $len - 1 ? $subChars[$j + 1] : null;
            
            $type = 'isolated';
            
            $prevConnects = ($prev && $this->isArabic($prev) && $this->connectsNext($prev));
            $nextConnects = ($next && $this->isArabic($next) && $this->connectsPrevious($next));
            
            if ($prevConnects && $nextConnects && $this->connectsPrevious($char) && $this->connectsNext($char)) {
                $type = 'medial';
            } elseif ($prevConnects && $this->connectsPrevious($char)) {
                $type = 'final';
            } elseif ($nextConnects && $this->connectsNext($char)) {
                $type = 'initial';
            }
            
            $reshapedSub[] = $this->getCharForm($char, $type);
        }
        
        return implode('', array_reverse($reshapedSub));
    }

    protected function mirrorBrackets($str)
    {
        $chars = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
        $result = '';
        foreach ($chars as $char) {
            if ($char === '(') $result .= ')';
            elseif ($char === ')') $result .= '(';
            elseif ($char === '[') $result .= ']';
            elseif ($char === ']') $result .= '[';
            elseif ($char === '{') $result .= '}';
            elseif ($char === '}') $result .= '{';
            elseif ($char === '<') $result .= '>';
            elseif ($char === '>') $result .= '<';
            else $result .= $char;
        }
        return $result;
    }

    protected function isArabic($char)
    {
        $ord = mb_ord($char);
        return ($ord >= 0x0600 && $ord <= 0x06FF) || ($ord >= 0xFB50 && $ord <= 0xFDFF) || ($ord >= 0xFE70 && $ord <= 0xFEFF);
    }
    
    protected function connectsNext($char)
    {
        $c = mb_ord($char);
        $non_connecting = [
            0x0622, 0x0623, 0x0624, 0x0625, 0x0627, 0x0629, 
            0x062F, 0x0630, 0x0631, 0x0632, 0x0648, 0x0671,
            0x0649
        ];
        
        if (in_array($c, $non_connecting)) return false;
        if ($c >= 0xFEF5 && $c <= 0xFEFC) return false;
        
        return true; 
    }

    protected function connectsPrevious($char)
    {
        return true;
    }

    protected function getCharForm($char, $type)
    {
        $c = mb_ord($char);
        $map = [
            0x0627 => [0xFE8D, 0xFE8D, 0xFE8E, 0xFE8E], // ALEF
            0x0628 => [0xFE8F, 0xFE91, 0xFE92, 0xFE90], // BEH
            0x062A => [0xFE95, 0xFE97, 0xFE98, 0xFE96], // TEH
            0x062B => [0xFE99, 0xFE9B, 0xFE9C, 0xFE9A], // THEH
            0x062C => [0xFE9D, 0xFE9F, 0xFEA0, 0xFE9E], // JEEM
            0x062D => [0xFEA1, 0xFEA3, 0xFEA4, 0xFEA2], // HAH
            0x062E => [0xFEA5, 0xFEA7, 0xFEA8, 0xFEA6], // KHAH
            0x062F => [0xFEA9, 0xFEA9, 0xFEAA, 0xFEAA], // DAL
            0x0630 => [0xFEAB, 0xFEAB, 0xFEAC, 0xFEAC], // THAL
            0x0631 => [0xFEAD, 0xFEAD, 0xFEAE, 0xFEAE], // REH
            0x0632 => [0xFEAF, 0xFEAF, 0xFEB0, 0xFEB0], // ZAIN
            0x0633 => [0xFEB1, 0xFEB3, 0xFEB4, 0xFEB2], // SEEN
            0x0634 => [0xFEB5, 0xFEB7, 0xFEB8, 0xFEB6], // SHEEN
            0x0635 => [0xFEB9, 0xFEBB, 0xFEBC, 0xFEBA], // SAD
            0x0636 => [0xFEBD, 0xFEBF, 0xFEC0, 0xFEBE], // DAD
            0x0637 => [0xFEC1, 0xFEC3, 0xFEC4, 0xFEC2], // TAH
            0x0638 => [0xFEC5, 0xFEC7, 0xFEC8, 0xFEC6], // ZAH
            0x0639 => [0xFEC9, 0xFECB, 0xFECC, 0xFECA], // AIN
            0x063A => [0xFECD, 0xFECF, 0xFED0, 0xFECE], // GHAIN
            0x0641 => [0xFED1, 0xFED3, 0xFED4, 0xFED2], // FEH
            0x0642 => [0xFED5, 0xFED7, 0xFED8, 0xFED6], // QAF
            0x0643 => [0xFED9, 0xFEDB, 0xFEDC, 0xFEDA], // KAF
            0x0644 => [0xFEDD, 0xFEDF, 0xFEE0, 0xFEDE], // LAM
            0x0645 => [0xFEE1, 0xFEE3, 0xFEE4, 0xFEE2], // MEEM
            0x0646 => [0xFEE5, 0xFEE7, 0xFEE8, 0xFEE6], // NOON
            0x0647 => [0xFEE9, 0xFEEB, 0xFEEC, 0xFEEA], // HEH
            0x0648 => [0xFEED, 0xFEED, 0xFEEE, 0xFEEE], // WAW
            0x0649 => [0xFEEF, 0xFEF3, 0xFEEF, 0xFEF0], // ALEF MAKSURA
            0x064A => [0xFEF1, 0xFEF3, 0xFEF4, 0xFEF2], // YEH
            0x0622 => [0xFE81, 0xFE81, 0xFE82, 0xFE82], // ALEF MADDA
            0x0623 => [0xFE83, 0xFE83, 0xFE84, 0xFE84], // ALEF HAMZA ABOVE
            0x0624 => [0xFE85, 0xFE85, 0xFE86, 0xFE86], // WAW HAMZA
            0x0625 => [0xFE87, 0xFE87, 0xFE88, 0xFE88], // ALEF HAMZA BELOW
            0x0626 => [0xFE89, 0xFE8B, 0xFE8C, 0xFE8A], // YEH HAMZA
            0x0629 => [0xFE93, 0xFE93, 0xFE94, 0xFE94], // TEH MARBUTA
        ];

        if (isset($map[$c])) {
            $idx = 0;
            switch($type) {
                case 'isolated': $idx = 0; break;
                case 'initial':  $idx = 1; break;
                case 'medial':   $idx = 2; break;
                case 'final':    $idx = 3; break;
            }
            return mb_chr($map[$c][$idx]);
        }

        return $char;
    }
}

// Test cases
$tests = [
    "النسبة 10% - 20% (جيد)",
    "الاستبيان (الأسئلة)",
    "هذا هو PDF جديد 100%",
    "الاسم: أحمد"
];

foreach ($tests as $t) {
    echo "Original: $t\n";
    echo "Reshaped: " . Arabic::reshape($t) . "\n\n";
}
