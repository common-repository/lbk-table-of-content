<?php

if ( !function_exists('lbk_get_headlines') ) {
    function lbk_get_headlines($html, $depth = 1) {
        if($depth > 7)
            return [];

        $headlines = explode('<h' . $depth, $html);

        unset($headlines[0]);       // contains only text before the first headline

        if(count($headlines) == 0)
            return [];

        $toc = [];      // will contain the (sub-) toc

        foreach($headlines as $key => $headline)
        {
            list($hl_info, $temp) = explode('>', $headline, 2);
            // $hl_info contains attributes of <hi ... > like the id.
            list($hl_text, $sub_content) = explode('</h' . $depth . '>', $temp, 2);
            // $hl contains the headline
            // $sub_content contains maybe other <hi>-tags
            $id = '';
            if(strlen($hl_info) > 0 && ($id_tag_pos = stripos($hl_info,'id')) !== false)
            {
                $id_start_pos = stripos($hl_info, '"', $id_tag_pos);
                $id_end_pos = stripos($hl_info, '"', $id_start_pos + 1);
                $id = substr($hl_info, $id_start_pos + 1, $id_end_pos - $id_start_pos - 1);
            }

            $sub_toc = lbk_get_headlines($sub_content, $depth + 1);

            $toc[] = [  
                'id'        => $id,
                'text'      => $hl_text,
                'sub_toc'   => $sub_toc
            ];
        }

        return $toc;
    }
}

if ( !function_exists( 'lbk_print_toc' ) ) {
    function lbk_print_toc($toc, $link_to_htmlpage = '', $depth = 1) {
        if(count($toc) == 0)
            return '';

        $toc_str = '';

        if($depth == 1) {
            $toc_str .= '<div class="lbk-toc">';
            $toc_str .= '<h3>Mục lục nội dung</h3>';
            $toc_str .= '<div class="lbk-toc-icon lbk-toc-icon-expand"></div>';
            $toc_str .= '<ol class="lbk-toc-expand">';
        } 
        else $toc_str .= '<ol style="display:none;">';

        foreach($toc as $headline)
        {
            $sub_toc = lbk_print_toc($headline['sub_toc'], $link_to_htmlpage, $depth+1);

            $toc_str .= '<li class="lbk-toc-hl heading-' . $depth . '">';
            if ($headline['id'] != '') {
                $toc_str .= '<a href="' . $link_to_htmlpage . '#' . $headline['id'] . '" class="lbk-toc-a">';
            }
            $toc_str .= $headline['text'];
            $toc_str .= ($headline['id'] != '') ? '</a>' : '';

            if ( $sub_toc != '' ) {
                $toc_str .= '<div class="lbk-toc-icon lbk-toc-icon-collapse"></div>';
                $toc_str .= $sub_toc;
            }

            $toc_str .= '</li>';
        }

        $toc_str .= '</ol>';

        if($depth == 1) 
            $toc_str .= '</div>';

        return $toc_str;
    }
}

if ( !function_exists('lbk_slugify') ) {
    function lbk_slugify($string) {
        $string = iconv('utf-8', 'us-ascii//translit//ignore', $string); // transliterate
        $string = str_replace("'", '', $string);
        $string = preg_replace('~[^\pL\d]+~u', '-', $string); // replace non letter or non digits by "-"
        $string = preg_replace('~[^-\w]+~', '', $string); // remove unwanted characters
        $string = preg_replace('~-+~', '-', $string); // remove duplicate "-"
        $string = trim($string, '-'); // trim "-"
        $string = trim($string); // trim
        $string = mb_strtolower($string, 'utf-8'); // lowercase
        
        return urlencode($string); // safe;
    }
}

if ( !function_exists('lbk_utf8convert') ) {
    function lbk_utf8convert($str) {
        if(!$str) return false;
    
        $utf8 = array(
    
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
    
            'd'=>'đ|Đ',
    
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
    
            'i'=>'í|ì|ỉ|ĩ|ị|Í|Ì|Ỉ|Ĩ|Ị',
    
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
    
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
    
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ|Ý|Ỳ|Ỷ|Ỹ|Ỵ',
    
        );
    
        foreach($utf8 as $ascii=>$uni) $str = preg_replace("/($uni)/i",$ascii,$str);
    
        return $str;
    }
}