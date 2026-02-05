<?php

namespace App\Helpers;

class Arabic
{
    public static function reshape($str)
    {
        if (! $str) {
            return $str;
        }

        $e = new self;

        return $e->utf8Glyphs($str);
    }

    protected function utf8Glyphs($str)
    {
        $str = (string) $str;
        
        // Basic Hebrew/Arabic range check to avoid processing English text extensively
        if (! preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]/u', $str)) {
            return $str;
        }

        $str = $this->utf8ToHif($str);
        
        // Split utf8 string into array of chars
        $chars = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
        $total = count($chars);
        $output = '';

        for ($i = 0; $i < $total; $i++) {
            $char = $chars[$i];
            
            // Check if char is Arabic
            if ($this->isArabic($char)) {
                $prev = $i > 0 ? $chars[$i - 1] : null;
                $next = $i < $total - 1 ? $chars[$i + 1] : null;

                $type = 'isolated';
                
                if ($prev && $next && $this->isArabic($prev) && $this->isArabic($next) && $this->connectsNext($prev) && $this->connectsPrevious($char) && $this->connectsNext($char) && $this->connectsPrevious($next)) {
                    $type = 'medial';
                } elseif ($prev && $this->isArabic($prev) && $this->connectsNext($prev) && $this->connectsPrevious($char)) {
                    $type = 'final';
                } elseif ($next && $this->isArabic($next) && $this->connectsNext($char) && $this->connectsPrevious($next)) {
                    $type = 'initial';
                }

                $output .= $this->getCharForm($char, $type);
            } else {
                $output .= $char;
            }
        }
        
        // Reverse for RTL visual support if mostly Arabic
        // Simple word reverse might be needed or full string reverse
        // For dompdf without RTL support, usually we accept visual ordering:
        // Identify runs of Arabic and reverse them? 
        // Or just reverse the whole string if it's an Arabic block?
        
        // Let's try reversing the Arabic words sequence or the whole string based on common solutions
        // Often we just reverse the whole string if it is primarily RTL.
        
        // Reversing the string (keeping unicode intact)
        preg_match_all('/./us', $output, $ar);
        
        // We need to handle mixed content carefully, but for now let's reverse the whole result
        // if it's primarily Arabic context.
        return implode('', array_reverse($ar[0]));
    }

    protected function isArabic($char)
    {
        $ord = mb_ord($char);
        return ($ord >= 0x0600 && $ord <= 0x06FF) || ($ord >= 0xFB50 && $ord <= 0xFDFF) || ($ord >= 0xFE70 && $ord <= 0xFEFF);
    }
    
    // Check if character connects to the next one (most do, some don't like ALEF)
    protected function connectsNext($char)
    {
        $c = mb_ord($char);
        // Characters that DO NOT connect to the next char
        // Alef, Dal, Thal, Ra, Zain, Waw, ...
        $non_connecting = [
            0x0622, 0x0623, 0x0624, 0x0625, 0x0627, 0x0629, 
            0x062F, 0x0630, 0x0631, 0x0632, 0x0648, 0x0671,
            0x0649 // Alef Maksura usually connects? No, rarely in middle. It behaves like Ya but only final? Actually usually final unless ligatured.
            // Let's stick to standard unconnectors
        ];
        
        if (in_array($c, $non_connecting)) return false;
        
        // Basic range check for standard arabic letters
        return true; 
    }

    protected function connectsPrevious($char)
    {
        return true; // Most connect from previous
    }

    // Simplified mapping for basic testing - In reality this table is huge
    protected function getCharForm($char, $type)
    {
        $c = mb_ord($char);
        
        // Map of standard char to [isolated, initial, medial, final]
        // Using FE range presentation forms
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
    
    // Normalize some chars
    protected function utf8ToHif($str) {
        return $str; // Basic for now
    }
}
