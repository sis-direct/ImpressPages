<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\standard\breadcrumb;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

class Template{

    public static function breadcrumb($breadCrumb, $separator = '', $showHome = true) {
        global $site;
        global $parametersMod;

        $answer = '';
        if ($showHome) {
            $answer .= '<a href="'.$site->generateUrl().'">'.$parametersMod->getValue('standard', 'configuration', 'translations', 'home').'</a>'."\n";
        }

        foreach ($breadCrumb as $key => $element) {
            if($answer != '') {
                $answer .= $separator;
            }
            $answer .= '<a href="'.$element->getLink().'" title="'.htmlspecialchars($element->getPageTitle()).'">'.htmlspecialchars($element->getButtonTitle()).'</a>'."\n";
        }
        return $answer;
    }
}