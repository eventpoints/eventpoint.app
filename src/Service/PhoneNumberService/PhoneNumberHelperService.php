<?php

declare(strict_types=1);

namespace App\Service\PhoneNumberService;

final class PhoneNumberHelperService
{
    public function getCodeWithoutPrefix(string $code): ?string
    {
        $trimmedCode = ltrim($code, '+');

        if (str_starts_with($trimmedCode, '00')) {
            $trimmedCode = substr($trimmedCode, 2);
        }

        return $trimmedCode ?: null;
    }

    public function getDialCode(string $phoneNumber): ?string
    {
        foreach ($this->getDialCodes() as $code => $country) {
            if (str_starts_with($phoneNumber, '+' . $code)) {
                return '+' . $code;
            } elseif (str_starts_with($phoneNumber, '00' . $code)) {
                return '00' . $code;
            }
        }

        return null;
    }

    public function getNumber(string $phoneNumber): ?string
    {
        foreach ($this->getDialCodes() as $code => $countryCode) {
            if (str_starts_with($phoneNumber, '+' . $code) || str_starts_with($phoneNumber, '00' . $code)) {
                $dialCodeLength = strlen($code) + (str_starts_with($phoneNumber, '+') ? 1 : 2);
                return substr($phoneNumber, $dialCodeLength);
            }
        }
        return null;
    }

    /**
     * @return string[]
     */
    public function getDialCodes(): array
    {
        return [
            '44' => 'GBR',
            '1' => 'USA',
            '213' => 'DZA',
            '376' => 'AND',
            '244' => 'AGO',
            '1264' => 'AIA',
            '1268' => 'ATG',
            '54' => 'ARG',
            '374' => 'ARM',
            '297' => 'ABW',
            '61' => 'AUS',
            '43' => 'AUT',
            '994' => 'AZE',
            '1242' => 'BHS',
            '973' => 'BHR',
            '880' => 'BGD',
            '1246' => 'BRB',
            '375' => 'BLR',
            '32' => 'BEL',
            '501' => 'BLZ',
            '229' => 'BEN',
            '1441' => 'BMU',
            '975' => 'BTN',
            '591' => 'BOL',
            '387' => 'BIH',
            '267' => 'BWA',
            '55' => 'BRA',
            '673' => 'BRN',
            '359' => 'BGR',
            '226' => 'BFA',
            '257' => 'BDI',
            '855' => 'KHM',
            '237' => 'CMR',
            '238' => 'CPV',
            '1345' => 'CYM',
            '236' => 'CAF',
            '56' => 'CHL',
            '86' => 'CHN',
            '57' => 'COL',
            '269' => 'COM',
            '242' => 'COG',
            '682' => 'COK',
            '506' => 'CRI',
            '385' => 'HRV',
            '53' => 'CUB',
            '357' => 'CYP',
            '420' => 'CZE',
            '45' => 'DNK',
            '253' => 'DJI',
            '1809' => 'DMA',
            '593' => 'ECU',
            '20' => 'EGY',
            '503' => 'SLV',
            '240' => 'GNQ',
            '291' => 'ERI',
            '372' => 'EST',
            '251' => 'ETH',
            '500' => 'FLK',
            '298' => 'FRO',
            '679' => 'FJI',
            '358' => 'FIN',
            '33' => 'FRA',
            '594' => 'GUF',
            '689' => 'PYF',
            '241' => 'GAB',
            '220' => 'GMB',
            '7880' => 'GEO',
            '49' => 'DEU',
            '233' => 'GHA',
            '350' => 'GIB',
            '30' => 'GRC',
            '299' => 'GRL',
            '1473' => 'GRD',
            '590' => 'GLP',
            '671' => 'GUM',
            '502' => 'GTM',
            '224' => 'GIN',
            '245' => 'GNB',
            '592' => 'GUY',
            '509' => 'HTI',
            '504' => 'HND',
            '852' => 'HKG',
            '36' => 'HUN',
            '354' => 'ISL',
            '91' => 'IND',
            '62' => 'IDN',
            '98' => 'IRN',
            '964' => 'IRQ',
            '353' => 'IRL',
            '972' => 'ISR',
            '39' => 'ITA',
            '1876' => 'JAM',
            '81' => 'JPN',
            '962' => 'JOR',
            '254' => 'KEN',
            '686' => 'KIR',
            '850' => 'PRK',
            '82' => 'KOR',
            '965' => 'KWT',
            '996' => 'KGZ',
            '856' => 'LAO',
            '371' => 'LVA',
            '961' => 'LBN',
            '266' => 'LSO',
            '231' => 'LBR',
            '218' => 'LBY',
            '417' => 'LIE',
            '370' => 'LTU',
            '352' => 'LUX',
            '853' => 'MAC',
            '389' => 'MKD',
            '261' => 'MDG',
            '265' => 'MWI',
            '60' => 'MYS',
            '960' => 'MDV',
            '223' => 'MLI',
            '356' => 'MLT',
            '692' => 'MHL',
            '596' => 'MTQ',
            '222' => 'MRT',
            '52' => 'MEX',
            '691' => 'FSM',
            '373' => 'MDA',
            '377' => 'MCO',
            '976' => 'MNG',
            '1664' => 'MSR',
            '212' => 'MAR',
            '258' => 'MOZ',
            '95' => 'MMR',
            '264' => 'NAM',
            '674' => 'NRU',
            '977' => 'NPL',
            '31' => 'NLD',
            '687' => 'NCL',
            '64' => 'NZL',
            '505' => 'NIC',
            '227' => 'NER',
            '234' => 'NGA',
            '683' => 'NIU',
            '672' => 'NFK',
            '670' => 'MNP',
            '47' => 'NOR',
            '968' => 'OMN',
            '680' => 'PLW',
            '507' => 'PAN',
            '675' => 'PNG',
            '595' => 'PRY',
            '51' => 'PER',
            '63' => 'PHL',
            '48' => 'POL',
            '351' => 'PRT',
            '1787' => 'PRI',
            '974' => 'QAT',
            '262' => 'REU',
            '40' => 'ROU',
            '7' => 'RUS',
            '250' => 'RWA',
            '378' => 'SMR',
            '239' => 'STP',
            '966' => 'SAU',
            '221' => 'SEN',
            '381' => 'SRB',
            '248' => 'SYC',
            '232' => 'SLE',
            '65' => 'SGP',
            '421' => 'SVK',
            '386' => 'SVN',
            '677' => 'SLB',
            '252' => 'SOM',
            '27' => 'ZAF',
            '34' => 'ESP',
            '94' => 'LKA',
            '290' => 'SHN',
            '1869' => 'KNA',
            '1758' => 'LCA',
            '249' => 'SDN',
            '597' => 'SUR',
            '268' => 'SWZ',
            '46' => 'SWE',
            '41' => 'CHE',
            '963' => 'SYR',
            '886' => 'TWN',
            '66' => 'THA',
            '228' => 'TGO',
            '676' => 'TON',
            '1868' => 'TTO',
            '216' => 'TUN',
            '90' => 'TUR',
            '993' => 'TKM',
            '1649' => 'TCA',
            '688' => 'TUV',
            '256' => 'UGA',
            '380' => 'UKR',
            '971' => 'ARE',
            '598' => 'URY',
            '678' => 'VUT',
            '379' => 'VAT',
            '58' => 'VEN',
            '84' => 'VNM',
            '681' => 'WLF',
            '969' => 'YEM',
            '967' => 'YEM',
            '260' => 'ZMB',
            '263' => 'ZWE',
        ];
    }
}
