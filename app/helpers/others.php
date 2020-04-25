<?php

/* Generate chart data for based on the date key and each of keys inside */
function get_chart_data(Array $main_array) {

    $results = [];

    foreach($main_array as $date_label => $data) {

        foreach($data as $label_key => $label_value) {

            if(!isset($results[$label_key])) {
                $results[$label_key] = [];
            }

            $results[$label_key][] = $label_value;

        }

    }

    foreach($results as $key => $value) {
        $results[$key] = '["' . implode('", "', $value) . '"]';
    }

    $results['labels'] = '["' . implode('", "', array_keys($main_array)) . '"]';

    return $results;
}

function get_gravatar($email, $s = 80, $d = 'mp', $r = 'g', $img = false, $atts = []) {
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5(strtolower(trim($email)));
    $url .= "?s=$s&d=$d&r=$r";

    if ($img) {
        $url = '<img src="' . $url . '"';

        foreach ($atts as $key => $val) {
            $url .= ' ' . $key . '="' . $val . '"';
        }

        $url .= ' />';
    }

    return $url;
}

function get_back_button($default = '') {

    return '<a href="' . url($default). '"><button class="btn btn-default btn-sm btn-get-back" data-toggle="tooltip" title="'. \Altum\Language::get()->global->go_back_button . '"><i class="fa fa-arrow-left"></i></button></a>';

}

function get_admin_options_button($type, $target_id) {

    switch($type) {

        case 'user' :
            return '
                <div class="dropdown">
                    <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
                        <i class="fas fa-ellipsis-v"></i>
                        
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="admin/user-view/' . $target_id . '"><i class="fa fa-eye"></i> ' . \Altum\Language::get()->global->view . '</a>
                            <a class="dropdown-item" href="admin/user-update/' . $target_id . '"><i class="fas fa-pencil-alt"></i> ' . \Altum\Language::get()->global->edit . '</a>
                            <a class="dropdown-item" data-confirm="' . \Altum\Language::get()->global->info_message->confirm_delete . '" href="admin/users/delete/' . $target_id . \Altum\Middlewares\Csrf::get_url_query() . '"><i class="fa fa-times"></i> ' . \Altum\Language::get()->global->delete . '</a>
                        </div>
                    </a>
                </div>';

            break;


        case 'link' :
            return '
                <div class="dropdown">
                    <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
                        <i class="fas fa-ellipsis-v"></i>
                        
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" data-confirm="' . \Altum\Language::get()->global->info_message->confirm_delete . '" href="admin/links/delete/' . $target_id . \Altum\Middlewares\Csrf::get_url_query() . '"><i class="fa fa-times"></i> ' . \Altum\Language::get()->global->delete . '</a>
                        </div>
                    </a>
                </div>';

            break;

        case 'domain' :
            return '
                <div class="dropdown">
                    <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
                        <i class="fas fa-ellipsis-v"></i>
                        
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="admin/domain-update/' . $target_id . '"><i class="fas fa-pencil-alt"></i> ' . \Altum\Language::get()->global->edit . '</a>
                            <a class="dropdown-item" data-confirm="' . \Altum\Language::get()->global->info_message->confirm_delete . '" href="admin/domains/delete/' . $target_id . \Altum\Middlewares\Csrf::get_url_query() . '"><i class="fa fa-times"></i> ' . \Altum\Language::get()->global->delete . '</a>
                        </div>
                    </a>
                </div>';

            break;


        case 'page' :
            return '
                <div class="dropdown">
                    <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
                        <i class="fas fa-ellipsis-v"></i>
                        
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="admin/page-update/' . $target_id . '"><i class="fas fa-pencil-alt"></i> ' . \Altum\Language::get()->global->edit . '</a>
                            <a class="dropdown-item" data-confirm="' . \Altum\Language::get()->global->info_message->confirm_delete . '" href="admin/pages/delete/' . $target_id . \Altum\Middlewares\Csrf::get_url_query() . '"><i class="fa fa-times"></i> ' . \Altum\Language::get()->global->delete . '</a>
                        </div>
                    </a>
                </div>';

            break;

        case 'package' :
            return '
                <div class="dropdown">
                    <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
                        <i class="fas fa-ellipsis-v"></i>
                        
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="admin/package-update/' . $target_id . '"><i class="fas fa-pencil-alt"></i> ' . \Altum\Language::get()->global->edit . '</a>
                            
                            ' . (is_numeric($target_id) ?
                                '<a class="dropdown-item" data-confirm="' . \Altum\Language::get()->global->info_message->confirm_delete . '" href="admin/packages/delete/' . $target_id . \Altum\Middlewares\Csrf::get_url_query() . '"><i class="fa fa-times"></i> ' . \Altum\Language::get()->global->delete . '</a>'
                                : null) . '
                            
                        </div>
                    </a>
                </div>';

            break;

    }
}

/* Helper to output proper and nice numbers */
function nr($number, $decimals = 0, $extra = false) {

    if($extra) {

        if(in_array('B', $extra)) {

            if($number > 999999999) {
                return floor($number / 1000000000) . 'B';
            }

        }

        if(in_array('M', $extra)) {

            if($number > 999999) {
                return floor($number / 1000000) . 'M';
            }

        }

        if(in_array('K', $extra)) {

            if($number > 999) {
                return floor($number / 1000) . 'K';
            }

        }

    }

    if($number == 0) {
        return 0;
    }

    return number_format($number, $decimals, \Altum\Language::get()->global->number->decimal_point, \Altum\Language::get()->global->number->thousands_separator);
}

function get_domain($url) {

    $host = parse_url($url, PHP_URL_HOST);

    $host = explode('.', $host);

    /* Return only the last 2 array values combined */
    return implode('.', array_slice($host, -2, 2));
}

function get_ip() {
    if(array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {

        if(strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            return trim(reset($ips));
        } else {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

    } else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
        return $_SERVER['REMOTE_ADDR'];
    } else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    return '';
}

function get_country_from_country_code($code) {
    $code = strtoupper($code);

    $country_list = [
        'AF' => 'Afghanistan',
        'AX' => 'Aland Islands',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AS' => 'American Samoa',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AQ' => 'Antarctica',
        'AG' => 'Antigua and Barbuda',
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AW' => 'Aruba',
        'AU' => 'Australia',
        'AT' => 'Austria',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas the',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        'BO' => 'Bolivia',
        'BA' => 'Bosnia and Herzegovina',
        'BW' => 'Botswana',
        'BV' => 'Bouvet Island (Bouvetoya)',
        'BR' => 'Brazil',
        'IO' => 'British Indian Ocean Territory (Chagos Archipelago)',
        'VG' => 'British Virgin Islands',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgaria',
        'BF' => 'Burkina Faso',
        'BI' => 'Burundi',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CA' => 'Canada',
        'CV' => 'Cape Verde',
        'KY' => 'Cayman Islands',
        'CF' => 'Central African Republic',
        'TD' => 'Chad',
        'CL' => 'Chile',
        'CN' => 'China',
        'CX' => 'Christmas Island',
        'CC' => 'Cocos (Keeling) Islands',
        'CO' => 'Colombia',
        'KM' => 'Comoros the',
        'CD' => 'Congo',
        'CG' => 'Congo the',
        'CK' => 'Cook Islands',
        'CR' => 'Costa Rica',
        'CI' => 'Cote d\'Ivoire',
        'HR' => 'Croatia',
        'CU' => 'Cuba',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'DJ' => 'Djibouti',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'GQ' => 'Equatorial Guinea',
        'ER' => 'Eritrea',
        'EE' => 'Estonia',
        'ET' => 'Ethiopia',
        'FO' => 'Faroe Islands',
        'FK' => 'Falkland Islands (Malvinas)',
        'FJ' => 'Fiji the Fiji Islands',
        'FI' => 'Finland',
        'FR' => 'France, French Republic',
        'GF' => 'French Guiana',
        'PF' => 'French Polynesia',
        'TF' => 'French Southern Territories',
        'GA' => 'Gabon',
        'GM' => 'Gambia the',
        'GE' => 'Georgia',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GR' => 'Greece',
        'GL' => 'Greenland',
        'GD' => 'Grenada',
        'GP' => 'Guadeloupe',
        'GU' => 'Guam',
        'GT' => 'Guatemala',
        'GG' => 'Guernsey',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HM' => 'Heard Island and McDonald Islands',
        'VA' => 'Holy See (Vatican City State)',
        'HN' => 'Honduras',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'IR' => 'Iran',
        'IQ' => 'Iraq',
        'IE' => 'Ireland',
        'IM' => 'Isle of Man',
        'IL' => 'Israel',
        'IT' => 'Italy',
        'JM' => 'Jamaica',
        'JP' => 'Japan',
        'JE' => 'Jersey',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'Kenya',
        'KI' => 'Kiribati',
        'KP' => 'Korea',
        'KR' => 'Korea',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyz Republic',
        'LA' => 'Lao',
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libyan Arab Jamahiriya',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MO' => 'Macao',
        'MK' => 'Macedonia',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MH' => 'Marshall Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'YT' => 'Mayotte',
        'MX' => 'Mexico',
        'FM' => 'Micronesia',
        'MD' => 'Moldova',
        'MC' => 'Monaco',
        'MN' => 'Mongolia',
        'ME' => 'Montenegro',
        'MS' => 'Montserrat',
        'MA' => 'Morocco',
        'MZ' => 'Mozambique',
        'MM' => 'Myanmar',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'AN' => 'Netherlands Antilles',
        'NL' => 'Netherlands the',
        'NC' => 'New Caledonia',
        'NZ' => 'New Zealand',
        'NI' => 'Nicaragua',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NU' => 'Niue',
        'NF' => 'Norfolk Island',
        'MP' => 'Northern Mariana Islands',
        'NO' => 'Norway',
        'OM' => 'Oman',
        'PK' => 'Pakistan',
        'PW' => 'Palau',
        'PS' => 'Palestinian Territory',
        'PA' => 'Panama',
        'PG' => 'Papua New Guinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PN' => 'Pitcairn Islands',
        'PL' => 'Poland',
        'PT' => 'Portugal, Portuguese Republic',
        'PR' => 'Puerto Rico',
        'QA' => 'Qatar',
        'RE' => 'Reunion',
        'RO' => 'Romania',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'BL' => 'Saint Barthelemy',
        'SH' => 'Saint Helena',
        'KN' => 'Saint Kitts and Nevis',
        'LC' => 'Saint Lucia',
        'MF' => 'Saint Martin',
        'PM' => 'Saint Pierre and Miquelon',
        'VC' => 'Saint Vincent and the Grenadines',
        'WS' => 'Samoa',
        'SM' => 'San Marino',
        'ST' => 'Sao Tome and Principe',
        'SA' => 'Saudi Arabia',
        'SN' => 'Senegal',
        'RS' => 'Serbia',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        'SK' => 'Slovakia (Slovak Republic)',
        'SI' => 'Slovenia',
        'SB' => 'Solomon Islands',
        'SO' => 'Somalia, Somali Republic',
        'ZA' => 'South Africa',
        'GS' => 'South Georgia and the South Sandwich Islands',
        'ES' => 'Spain',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SJ' => 'Svalbard & Jan Mayen Islands',
        'SZ' => 'Swaziland',
        'SE' => 'Sweden',
        'CH' => 'Switzerland, Swiss Confederation',
        'SY' => 'Syrian Arab Republic',
        'TW' => 'Taiwan',
        'TJ' => 'Tajikistan',
        'TZ' => 'Tanzania',
        'TH' => 'Thailand',
        'TL' => 'Timor-Leste',
        'TG' => 'Togo',
        'TK' => 'Tokelau',
        'TO' => 'Tonga',
        'TT' => 'Trinidad and Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks and Caicos Islands',
        'TV' => 'Tuvalu',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'AE' => 'United Arab Emirates',
        'GB' => 'United Kingdom',
        'US' => 'United States of America',
        'UM' => 'United States Minor Outlying Islands',
        'VI' => 'United States Virgin Islands',
        'UY' => 'Uruguay, Eastern Republic of',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        'VE' => 'Venezuela',
        'VN' => 'Vietnam',
        'WF' => 'Wallis and Futuna',
        'EH' => 'Western Sahara',
        'YE' => 'Yemen',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe'
    ];

    if(!$country_list[$code]) {
        return $code;
    } else {
        return $country_list[$code];
    }
}
