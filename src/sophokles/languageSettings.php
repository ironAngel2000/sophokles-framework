<?php

namespace Sophokles\Sophokles;

final class languageSettings
{

    const DEUTSCH = 'de';
    const SPANISCH = 'es';
    const SPANISCH_MX = 'mx';
    const FRANZOESISCH = 'fr';
    const FRANZOESISCH_CA = 'ca';
    const ITALIENISCH = 'it';
    const ENGLISCH = 'en';
    const ENGLISCH_USA = 'us';
    const ENGLISCH_GB = 'uk';
    const CHINESISCH = 'zh';
    const CHINESISCH_TRADITIONEL = 'tn';
    const RUSSISCH = 'ru';
    const PORTUGISISCH = 'pt';
    const TUERKISCH = 'tr';
    const POLNISCH = 'pl';
    const HOLLAENDISCH = 'nl';
    const UNGARISCH = 'hu';
    const TSCHECHISCH = 'cs';
    const DAENISCH = 'da';
    const SCHWAEDISCH = 'sv';


    private $aktLang;
    private $text_de;
    private $text_lng;
    private $lcall;
    private $lcspr;
    private $intNr;


    public function __construct($sprache){
        $this->aktLang = $sprache;

        switch (trim($this->aktLang)){
            case languageSettings::DEUTSCH:
                $this->intNr = 1;
                $this->text_de = 'deutsch';
                $this->text_lng = 'deutsch';
                $this->lcall= 'de_DE';
                $this->lcspr= 'deu_deu';
                break;
            case languageSettings::ENGLISCH:
                $this->intNr = 2;
                $this->text_de = 'englisch';
                $this->text_lng = 'english';
                $this->lcall= 'en_GB';
                $this->lcspr= 'eng_eng';
                break;
            case languageSettings::ENGLISCH_USA:
                $this->intNr = 3;
                $this->text_de = 'USA';
                $this->text_lng = 'USA';
                $this->lcall= 'en_US';
                $this->lcspr= 'eng_usa';
                break;
            case languageSettings::FRANZOESISCH:
                $this->intNr = 4;
                $this->text_de = 'franz&ouml;sisch';
                $this->text_lng = 'fran&ccedil;ais';
                $this->lcall= 'fr_FR';
                $this->lcspr= 'fra_fra';
                break;
            case languageSettings::ITALIENISCH:
                $this->intNr = 5;
                $this->text_de = 'italienisch';
                $this->text_lng = 'italiano';
                $this->lcall= 'it_IT';
                $this->lcspr= 'ita_ita';
                break;
            case languageSettings::SPANISCH:
                $this->intNr = 6;
                $this->text_de = 'spanisch';
                $this->text_lng = 'espa&ntilde;ol';
                $this->lcall= 'es_ES';
                $this->lcspr= 'esl_esl';
                break;
            case languageSettings::RUSSISCH:
                $this->intNr = 7;
                $this->text_de = 'russisch';
                $this->text_lng = 'pycc&#1082;&#1080;&#1081;';
                $this->lcall= 'ru_RU';
                $this->lcspr= 'rus_rus';
                break;
            case languageSettings::HOLLAENDISCH:
                $this->intNr = 8;
                $this->text_de = 'holl&auml;ndisch';
                $this->text_lng = 'nederlands';
                $this->lcall= 'nl_NL';
                $this->lcspr= 'dut_dut';
                break;
            case languageSettings::TSCHECHISCH:
                $this->intNr = 9;
                $this->text_de = 'tschechisch';
                $this->text_lng = '&#269;esky';
                $this->lcall= 'cs_CZ';
                $this->lcspr= 'ces_ces';
                break;
            case languageSettings::UNGARISCH:
                $this->intNr = 10;
                $this->text_de = 'ungarisch';
                $this->text_lng = 'magyar';
                $this->lcall= 'hu_HU';
                $this->lcspr= 'hun_hun';
                break;
            case languageSettings::CHINESISCH:
                $this->intNr = 11;
                $this->text_de = 'chinesisch';
                $this->text_lng = '&#31616;&#20307;&#20013;&#25991;';
                $this->lcall= 'zh_CN';
                $this->lcspr= 'chi_chi';
                break;
            case languageSettings::POLNISCH:
                $this->intNr = 12;
                $this->text_de = 'Polnisch';
                $this->text_lng = 'polski';
                $this->lcall= 'pl_PL';
                $this->lcspr= 'pol_pol';
                break;
            case languageSettings::DAENISCH:
                $this->intNr = 13;
                $this->text_de = 'd&auml;nisch';
                $this->text_lng = 'dansk';
                $this->lcall= 'da_DK';
                $this->lcspr= 'dan_dan';
                break;
            case languageSettings::SCHWAEDISCH:
                $this->intNr = 14;
                $this->text_de = 'schwedisch';
                $this->text_lng = 'svenska';
                $this->lcall= 'sv_SE';
                $this->lcspr= 'sve_sve';
                break;
            case languageSettings::TUERKISCH:
                $this->intNr = 15;
                $this->text_de = 't&uuml;rkisch';
                $this->text_lng = 't&uuml;rk';
                $this->lcall= 'tr_TR';
                $this->lcspr= 'tur_tur';
                break;
            case languageSettings::PORTUGISISCH:
                $this->intNr = 16;
                $this->text_de = 'protugiesisch';
                $this->text_lng = 'portugu&ecirc;s';
                $this->lcall= 'pt_PT';
                $this->lcspr= 'por_por';
                break;
            case languageSettings::ENGLISCH_GB:
                $this->intNr = 17;
                $this->text_de = 'englisch_britisch';
                $this->text_lng = 'Great Britain';
                $this->lcall= 'en_GB';
                $this->lcspr= 'eng_eng';
                break;
            case languageSettings::CHINESISCH_TRADITIONEL:
                $this->intNr = 18;
                $this->text_de = 'chinesisch traditionell';
                $this->text_lng = '&#32321;&#39636;&#20013;&#25991;';
                $this->lcall= 'zh_CN';
                $this->lcspr= 'chi_chi';
                break;
            case languageSettings::FRANZOESISCH_CA:
                $this->intNr = 19;
                $this->text_de = 'franz&ouml;sisch canadisch';
                $this->text_lng = 'fran&ccedil;ais canadien';
                $this->lcall= 'fr_CA';
                $this->lcspr= 'fra_fra';
                break;
            case languageSettings::SPANISCH_MX:
                $this->intNr = 20;
                $this->text_de = 'spanisch mexikanisch';
                $this->text_lng = 'espa&ntilde;ol mexicano';
                $this->lcall= 'es_MX';
                $this->lcspr= 'esl_esl';
                break;
            default:
                $failure = 'languages.php - '.'Sprache ('.trim($this->aktLang).') im System nicht definiert!'."\r\n";
                trigger_error($failure, E_USER_ERROR);
                break;
        }

    }

    public function getName($deutsch=false){
        if($deutsch===true) return $this->text_de;
        else return $this->text_lng;
    }

    public function getNumber($exp=false){
        if($exp===true) return pow(2,($this->intNr-1));
        else return $this->intNr;
    }

    public function getLCALL(){
        return $this->lcall;
    }

    public function getLCALLSP(){
        return $this->lcspr;
    }

    public function getCode(){
        return trim($this->aktLang);
    }

}
