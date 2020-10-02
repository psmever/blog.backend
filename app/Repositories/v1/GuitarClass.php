<?php

namespace App\Repositories\v1;

class GuitarClass
{
    /**
     * 테스트용.
     *
     * @return void
     */
    public function GuitarTest()
    {
        return "GuitarTest()";
    }

    /**
     * 한글 슬러그
     *
     * https://blog.asamaru.net/2016/11/21/php-hangul-slug/
     * 소스 복붙. 약간 수정. 감사합니다 (__)
     *
     * @param [type] $str
     * @param array $options
     * @return string
     */
    public function convertSlugString($str, $options = []) : string
    {

        // $slug = preg_replace('/\?/u', ' ', trim($str));
        // $slug = preg_replace('/\s+/u', '-', trim($slug));

        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());

        $defaults = [
            'delimiter' => '-',
            'limit' => null,
            'lowercase' => true,
            'replacements' => [],
            'transliterate' => false, // 영어로 바꿔주는 옵션.
        ];
        // Merge options
        $options = array_merge($defaults, $options);

        // Make custom replacements
        $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

        // Transliterate characters to ASCII
        if ($options['transliterate']) {
            $str = static::convertHangulToEnglish($str);
        }

        // Replace non-alphanumeric characters with our delimiter
        $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

        // Remove duplicate delimiters
        $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

        // Truncate slug to max. characters
        $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

        // Remove delimiter from ends
        $str = trim($str, $options['delimiter']);

        return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
    }

    static public function convertHangulToEnglish($text)
    {
        /* 초중성에 대응하는 영문 알파벳 배열화 */
        // $LCtable = array("ㄱ", "ㄲ", "ㄴ", "ㄷ", "ㄸ", "ㄹ", "ㅁ", "ㅂ", "ㅃ", "ㅅ", "ㅆ", "ㅇ", "ㅈ", "ㅉ", "ㅊ", "ㅋ", "ㅌ", "ㅍ", "ㅎ");
        // $MVtable = array("ㅏ", "ㅐ", "ㅑ", "ㅒ", "ㅓ", "ㅔ", "ㅕ", "ㅖ", "ㅗ", "ㅘ", "ㅙ", "ㅚ", "ㅛ", "ㅜ", "ㅝ", "ㅞ", "ㅟ", "ㅠ", "ㅡ", "ㅢ", "ㅣ");
        // $TCtable = array("", "ㄱ", "ㄲ", "ㄳ", "ㄴ", "ㄵ", "ㄶ", "ㄷ", "ㄹ", "ㄺ", "ㄻ", "ㄼ", "ㄽ", "ㄾ", "ㄿ", "ㅀ", "ㅁ", "ㅂ", "ㅄ", "ㅅ", "ㅆ", "ㅇ", "ㅈ", "ㅊ", "ㅋ", "ㅌ", "ㅍ", "ㅎ");

        $LCetable = ["k", "kk", "n", "d", "tt", "l", "m", "b", "pp", "s", "ss", "", "j", "jj", "ch", "k", "t", "p", "h"];
        $MVetable = ["a", "ae", "ya", "yae", "eo", "e", "yeo", "ye", "o", "wa", "wae", "oe", "yo", "u", "wo", "we", "wi", "yu", "eu", "ui", "i"];
        $TCetable = ["", "g", "kk", "k", "n", "n", "n", "t", "l", "l", "l", "l", "l", "l", "l", "l", "m", "p", "p", "s", "ss", "ng", "j", "ch", "k", "t", "p", "h"];

        $returnValue = '';

        // UTF-8로 변환된 문장을 유니코드로 변환한다.
        $result = static::convertUtf8ToUnicode($text);

        // 유니코드로 변환된 글이 한글코드 안에 있으면 초중성으로 분리한다
        // 원본에서 약간 수정함. 한글 외 글자에서 중복패턴이 나오는 부분 수정함.
        // 단, 한글외 [0-9a-Z]는 확인했지만 그 외 문자에서는 확인 해 보지 않음.
        foreach ($result AS $key => $val) {
            if ($val >= 44032 && $val <= 55203) {
                $chr = "";
                $code = $val;
                $temp1 = $code - 44032;
                $T = (int)$temp1 % 28;
                $temp1 /= 28;
                $V = (int)$temp1 % 21;
                $temp1 /= 21;
                $L = (int)$temp1;
                $chr .= $LCetable[$L] . $MVetable[$V] . $TCetable[$T];

                $returnValue .= ucfirst($chr);
            } else {
                $returnValue .= chr($val);
            }
        }
        return $returnValue;
    }

    static public function convertUtf8ToUnicode($str)
    {
        $result = [];
        $values = [];
        $lookingFor = 1;

        for ($i = 0; $i < strlen($str); $i++) {
            $thisValue = ord($str[$i]);

            if ($thisValue < 128) {
                $result[] = $thisValue;
            } else {

                if (count($values) == 0) {
                    $lookingFor = ($thisValue < 224) ? 2 : 3;
                }

                $values[] = $thisValue;

                if (count($values) == $lookingFor) {

                    $number = ($lookingFor == 3) ?
                        (($values[0] % 16) * 4096) + (($values[1] % 64) * 64) + ($values[2] % 64) :
                        (($values[0] % 32) * 64) + ($values[1] % 64);

                    $result[] = $number;
                    $values = [];
                    $lookingFor = 1;
                }
            }
        }

        return $result;
    }

    /**
     * 시간은 문자열로 변환
     *
     * @param string $timestamp
     * @return string
     */
    static public function convertTimeToString($timestamp = NULL) : string
    {
        $returnStr = '';

        if(!ctype_digit($timestamp)) {
            $timestamp = strtotime($timestamp);
        }

        $diff = time() - $timestamp;

        if($diff == 0) {
            return '지금';
        } elseif($diff > 0) {

            $day_diff = floor($diff / 86400);

            if($day_diff == 0) {
                if($diff < 60) return '조금 전';
                if($diff < 120) return '1분전';
                if($diff < 3600) return floor($diff / 60) . '분전';
                if($diff < 7200) return '1시간 전';
                if($diff < 86400) return floor($diff / 3600) . '시간 전';
            }

            if($day_diff == 1) { return '어제'; }
            if($day_diff < 7) { return $day_diff . '일 전'; }
            if($day_diff < 31) { return ceil($day_diff / 7) . '주 전'; }
            if($day_diff < 60) { return '지난 달'; }

            $returnStr = date('F Y', $timestamp);

        } else {

            $diff = abs($diff);
            $day_diff = floor($diff / 86400);

            if($day_diff == 0) {
                if($diff < 120) { return '분 안에'; }
                if($diff < 3600) { return ' ' . floor($diff / 60) . ' 분 안에'; }
                if($diff < 7200) { return '1시간 안에'; }
                if($diff < 86400) { return ' ' . floor($diff / 3600) . ' 시간 안에'; }
            }

            if($day_diff == 1) { return '내일'; }
            if($day_diff < 4) { return date('l', $timestamp); }
            if($day_diff < 7 + (7 - date('w'))) { return '다음주'; }
            if(ceil($day_diff / 7) < 4) { return '' . ceil($day_diff / 7) . '주 안에'; }
            if(date('n', $timestamp) == date('n') + 1) { return '다음달'; }

            $returnStr = date('F Y', $timestamp);
        }

        return $returnStr;
    }
}
