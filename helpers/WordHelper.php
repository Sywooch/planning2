<?php

namespace app\helpers;
use Yii;

class WordHelper {

    public static function Genitive($word)
    {
        //ИСКЛЮЧЕНИЯ В СИСТЕМЕ
        if(in_array($word,array('пресс-служба')))
            return 'пресс-службы';

        $punctuation = (in_array(mb_substr($word, -1, 1, "UTF-8"), [',', '.', '!', '?']))? mb_substr($word, -1, 1, "UTF-8"): '';
        if($punctuation!='')
            $word = self::cutWordRight($word,1);
        if(in_array(mb_strtoupper($word, "UTF-8"), ['ЗАГС','ГОЧС','ЗОЖ','ДТиСР','СДЮСШОР','ВИТИ','НИЯУ','МИФИ','(ОФИУМС)','СМИ' ,'МКУ',
          '«ДСиГХ»', 'МО', 'УФСБ', 'КУИ', 'УФМС', 'ИКРО', 'ТИК', 'ГАС','ЖКХ','ТОСам', 'ГКУ', 'РО', 'ЗАО', 'ООО', 'ФГУП', 'ГБУК', 'ИФНС',
          'СОН', 'ГБУ', 'ВФ', 'МСЧ', 'ФГБУЗ', 'НКЦ', 'ФМБА', 'МБУДО', 'ДООЦ']))
            return mb_strtoupper($word, "UTF-8");
        $lastChar = mb_substr($word, -1, 1, "UTF-8");
        $lastTwoChars = mb_substr($word, -2, 2, "UTF-8");
        $lastThreeChars = mb_substr($word, -3, 3, "UTF-8");

        $consonants = ['ц', 'к', 'н', 'г', 'ш', 'щ', 'з', 'ф', 'п', 'р', 'л', 'д', 'ж', 'в', 'с', 'т', 'б'];
        //$vowels = ['у', 'е', 'а', 'о', 'э', 'я', 'и', 'ю'];
        if(in_array($lastTwoChars, ['ен', 'ов', 'ии', 'ой', 'их', 'ых', 'ей', 'ым', 'ам', 'ев']) ||
          in_array($lastChar, ['ы', 'ю']) ||
          in_array($lastThreeChars, ['ами', 'ями' , 'ыми', 'ими', 'ого', 'его', 'ому']) ||
          in_array($word, ['по', 'за', 'и', 'с', 'технологий', 'образования',
            'здравоохранения', 'развития', 'города', 'Волгодонска', 'учета', 'труда','предпринимательства',
            'туризма', 'самоуправления', 'Мэра', 'обслуживания', 'рынка', 'услуг', 'прав', 'Комитета','порядка',
            'отношений','г','г.Волгодонска', 'попечительства', 'имущества', 'в', 'закупок','формирования','Управления',
            'комиссариата','Волгодонску', 'организаций', 'сил','пособий', 'ухода', 'благоустройства', 'ремонта', 'юношества',
            'надзора','землеустройства','обеспечения'])) //@todo По возможности перенести в файл или базу
            return $word.$punctuation;
        if(in_array($lastChar,$consonants))
            return $word.'а'.$punctuation;
        switch($lastThreeChars)
        {
            case 'кий':
            case 'кой':
                return self::cutWordRight($word,3).'кого'.$punctuation;
                break;
        }
        switch ($lastTwoChars){
            case 'ая':
                return self::cutWordRight($word,2).'ой'.$punctuation;
                break;
            case 'ия':
                return self::cutWordRight($word,2).'ии'.$punctuation;
                break;
            case 'ый':
            case 'ое':
                return self::cutWordRight($word,2).'ого'.$punctuation;
                break;
            case 'ий':
                return self::cutWordRight($word,2).'его'.$punctuation;
                break;
            case 'ие':
                return self::cutWordRight($word,2).'ия'.$punctuation;
                break;
            case 'ка':
                return self::cutWordRight($word,2).'ки'.$punctuation;
                break;
        }
        if(in_array($lastChar, ['а']))
            return self::cutWordRight($word,1).'ы';
        if(in_array($lastChar, ['ь']))
            return self::cutWordRight($word,1).'я';
        return $word.$punctuation;
    }

    public static function changeQuotes($str) {
        $str = preg_replace('/(^|\s|\()"/','$1«',$str);
        $str = preg_replace('/"(\;|\!|\?|\:|\.|\,|$|\)|\s)/','»$1',$str);
        return $str;
    }

    public static function mb_ucfirst($str)
    {
        if(explode(' ',$str)[0]!="Мэр")
            $str = mb_strtolower(mb_substr($str, 0, 1,'UTF-8'),'UTF-8') . mb_substr($str, 1,strlen($str)-1,'UTF-8');
        return $str;
    }

    public static function cutWordRight($word, $length=0){
        return mb_substr($word,0,mb_strlen($word, "UTF-8")-$length,"UTF-8");
    }

    public static function quoteString($str){
        return mb_convert_encoding('&#171;', 'UTF-8', 'HTML-ENTITIES').$str.mb_convert_encoding('&#187;', 'UTF-8', 'HTML-ENTITIES');
    }
} 