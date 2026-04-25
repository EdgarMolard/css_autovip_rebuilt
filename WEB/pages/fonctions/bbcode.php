<?php

function BBcode($string) {
    $string = nl2br($string);
    $format_search = array(
        '#\[b\](.*?)\[/b\]#is', // Bold ([b]text[/b]
        '#\[i\](.*?)\[/i\]#is', // Italics ([i]text[/i]
        '#\[u\](.*?)\[/u\]#is', // Underline ([u]text[/u])
        '#\[s\](.*?)\[/s\]#is', // Strikethrough ([s]text[/s])
        '#\[quote\](.*?)\[/quote\]#is', // Quote ([quote]text[/quote])
        '#\[code\](.*?)\[/code\]#is', // Monospaced code [code]text[/code])
        '#\[size=([1-9]|1[0-9]|20)\](.*?)\[/size\]#is', // Font size 1-20px [size=20]text[/size])
        '#\[color=\#?([A-F0-9]{3}|[A-F0-9]{6})\](.*?)\[/color\]#is', // Font color ([color=#00F]text[/color])
        '#\[url=((?:ftp|https?)://.*?)\](.*?)\[/url\]#i', // Hyperlink with descriptive text ([url=http://url]text[/url])
        '#\[url\]((?:ftp|https?)://.*?)\[/url\]#i', // Hyperlink ([url]http://url[/url])
        '#\[img\](https?://.*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]#i', // Image ([img]http://url_to_image[/img])
        '#\:agr:#is', // Agressive
        '#\:@#is', // Colere
        '#\:D#is', // Big Smile
        '#\;\)#is', // Blink
        '#\:\)#is', // Smile
        '#\:o#is', // Amazed
        '#\:8#is', // Ignore
        '#\:X#is' // ><
    );
    // The matching array of strings to replace matches with
    $format_replace = array(
        '<strong>$1</strong>',
        '<em>$1</em>',
        '<span style="text-decoration: underline;">$1</span>',
        '<span style="text-decoration: line-through;">$1</span>',
        '<blockquote>$1</blockquote>',
        '<pre>$1</' . 'pre>',
        '<span style="font-size: $1px;">$2</span>',
        '<span style="color: #$1;">$2</span>',
        '<a href="$1">$2</a>',
        '<a href="$1">$1</a>',
        '<img src="$1" alt="" />',
        '<img src="img/smileys/Aggressive.png" alt="Agressive" />',
        '<img src="img/smileys/Angry.png" alt="Angry" />',
        '<img src="img/smileys/Big Grin.png" alt="Big Grin" />',
        '<img src="img/smileys/Blink.png" alt="Blink" />',
        '<img src="img/smileys/Cool.png" alt="Cool" />',
        '<img src="img/smileys/Bored.png" alt="Bored" />',
        '<img src="img/smileys/Giggle.png" alt="Giggle" />',
        '<img src="img/smileys/Dead Bunneh.png" alt="Dead Bunneh" />'
    );
    $string = preg_replace($format_search, $format_replace, $string);
    return $string;
}

?>