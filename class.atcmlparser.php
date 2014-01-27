<?php
/**
 * ATCML Parser
 * Simple ATCML parser. 
 * @author Vincent Buzzano, ATC Future Medias SA
 * @version 1.0 - 2014-01-27 
 */
class ATCMLParser {

  /**
   * Parse Content
   * @param xml (SimpleXML)
   * @return array
   */
  function parseContent($file) {
    // load xml file
    $xml = simplexml_load_file($file);

    if ($xml == null)
      throw new Exception("Cannot read xml file");

    // add namespace
    $xml->registerXPathNamespace('c', 'http://www.allthecontent.com/xml/delivery/generic/contents');

    // read content
    $content = array();
    $content['uid']             = $this->getUID($xml);
    $content['title']           = $this->getTitle($xml);
    $content['description']     = $this->getDescription($xml);
    $content['credits']         = $this->getCredits($xml);
    $content['publicationDate'] = $this->getPubDate($xml);
    $content['language']        = $this->getLang($xml);
    $content['contentType']     = $this->getContentType($xml);
    $content['coreMedia']       = $this->getCoreMedia($xml);
    $content['format']          = $this->getFormat($xml);
    $content['themes']          = $this->getThemes($xml);
    $content['license']         = $this->getLicense($xml);
    $content['keywords']        = $this->getKeywords($xml);
    $content['tags']            = $this->getTags($xml);
    $content['characters']      = $this->getCharactersCount($xml);
    $content['words']           = $this->getWordsCount($xml);
    $content['links']           = $this->getLinks($xml);
    $content['attachments']     = $this->getAttachments($xml);
    $content['items']           = $this->getItems($xml);

    return $content;  
  }


  /**
   * Get UID
   * @param xml (SimpleXML)
   * @return uid
   */
  private function getUID($xml) {
    return $this->getNodeText($xml, "/c:allthecontent/c:content/@uid", null);
  }

  /**
   * Get Title
   * @param xml (SimpleXML)
   * @return title
   */
  private function getTitle($xml) {
    return $this->getNodeText($xml, "/c:allthecontent/c:content/c:title", "Untitled");
  }

  /**
   * Get Description
   * @param xml (SimpleXML)
   * @return description
   */
  private function getDescription($xml) {
    return $this->getNodeText($xml, "/c:allthecontent/c:content/c:description", null);
  }

  /**
   * Get Credits
   * @param xml (SimpleXML)
   * @return credits
   */
  private function getCredits($xml) {
    return $this->getNodeText($xml, "/c:allthecontent/c:content/c:credits", null);
  }

  /**
   * Get Publication date
   * @param xml (SimpleXML)
   * @return time
   */
  private function getPubDate($xml) {
    return $this->getNodeDate($xml, "/c:allthecontent/c:content/c:pubdate", time());
  }

  /**
   * Get Language
   * @param xml (SimpleXML)
   * @return lang code
   */
  private function getLang($xml) {
    return $this->getNodeText($xml, "/c:allthecontent/c:content/c:lang[code]", "ab");
  }

  /**
   * Get ContentType
   * @param xml (SimpleXML)
   * @return code
   */
  private function getContentType($xml) {
    return $this->getNodeText($xml, "/c:allthecontent/c:content/c:contenttype/@code", "");
  }

  /**
   * Get Coremedia
   * @param xml (SimpleXML)
   * @return code
   */
  private function getCoreMedia($xml) {
    return $this->getNodeText($xml, "/c:allthecontent/c:content/c:coremedia/@code", "");
  }

  /**
   * Get Format
   * @param xml (SimpleXML)
   * @return code
   */
  function getFormat($xml) {
    return $this->getNodeText($xml, "/c:allthecontent/c:content/c:format/@code", "");
  }

  /**
   * Get License
   * @param xml (SimpleXML)
   * @return array
   */
  private function getLicense($xml) {
    $l = $this->getNode($xml, "/c:allthecontent/c:content/c:license");
    $v = array();
    foreach($l->children() as $k)
      $v[$k->getName()] = ($k->__toString() == "true" ? "yes":"no");
    return $v;
  }

  /**
   * Get KeyWords
   * @param xml (SimpleXML)
   * @return array
   */
  private function getKeywords($xml) {
    $l = $this->getNode($xml, "/c:allthecontent/c:content/c:keywords");
    $v = array();
    foreach($l->children() as $k)
      $v[] = $k->__toString();
    return $v;
  }

  /**
   * Get Tags
   * @param xml (SimpleXML)
   * @return array(array)
   */
  private function getTags($xml) {
    $l = $this->getNodeList($xml, "/c:allthecontent/c:content/c:tag");
    $tags = array();
    foreach($l as $t) {
      $atts = $t->attributes();
      $code = $atts['code']->__toString();
      if ($code == 'characters' || $code == 'words')
        continue;

      $values = array();
      foreach($t->children() as $v) {
        $values[] = $v->__toString();
      }
      $tags[$code] = $values;
    }
    return $tags;
  }

  /**
   * Get Links
   * @param xml (SimpleXML)
   * @return array(array)
   */
  private function getLinks($xml) {
    $l = $this->getNodeList($xml, "/c:allthecontent/c:content/c:link");
    $links = array();
    foreach($l as $t) {
      $atts = $t->attributes();
      $url = $atts['url']->__toString();
      $name = $t->__toString();
      $links[$url] = $name;
    }
    return $links;
  }

  /**
   * Get Attachments
   * @param xml (SimpleXML)
   * @return array(array)
   */
  private function getAttachments($xml) {
    $l = $this->getNodeList($xml, "/c:allthecontent/c:content/c:attachment");
    $attachments = array();
    foreach($l as $t) {
      $atts = $t->attributes();
      $attachment = array();
      $attachment['uid']      = $atts['uid']->__toString();
      $attachment['type']     = $atts['type']->__toString();
      $attachment['format']   = $atts['format']->__toString();
      $attachment['mimetype'] = $atts['mimetype']->__toString();
     
      foreach($t->children() as $child) {
        if ($child->getName() == "description")
          $attachment['description'] = $child->__toString();
        else if (($child->getName() == "credits"))
          $attachment['credits'] = $child->__toString();
      }

      $attachment['filename'] = $atts['filename']->__toString();

      $attachments[] = $attachment;
    }
    return $attachments;
  }

  /**
   * Get Items
   * @param xml (SimpleXML)
   * @return array(array)
   */
  private function getItems($xml) {
    $l = $this->getNodeList($xml, "/c:allthecontent/c:content/c:item");
    $items = array();
    foreach($l as $t) {
      $atts = $t->attributes();
      $item = array();
      $item['uid']      = $atts['uid']->__toString();
      $item['mimetype'] = $atts['mimetype']->__toString();
      $item['filename'] = $atts['filename']->__toString();
      $items[] = $item;
    }
    return $items;
  }

  /**
   * Get Number of characters in the content
   * @param xml (SimpleXML)
   * @return number
   */
  private function getCharactersCount($xml) {
    $l = $this->getNodeList($xml, "/c:allthecontent/c:content/c:tag[@code='characters']");
    if (count($l) == 0) return 0;
    $n = $l[0]->children();
    if (count($n) == 0) return 0;
    return intval($n[0]->__toString());
  }

  /**
   * Get Number of words in the content
   * @param xml (SimpleXML)
   * @return number
   */
  private function getWordsCount($xml) {
    $l = $this->getNodeList($xml, "/c:allthecontent/c:content/c:tag[@code='words']");
    if (count($l) == 0) return 0;
    $n = $l[0]->children();
    if (count($n) == 0) return 0;
    return intval($n[0]->__toString());
  }


  /**
   * Get Themes
   * @param xml (SimpleXML)
   * @return code
   */
  private function getThemes($xml) {
    $result = $this->getNodeList($xml, "/c:allthecontent/c:content/c:theme/@code");
    $ar = array();
    foreach($result as $theme) {
      $ar[] = $theme->__toString();
    }
    return $ar;
  }

  private function getNodeText($xml, $xpath, $default) {
    $result = $this->getNode($xml, $xpath);
    if ($result) {
      return $result->__toString();
    } else return $default;
  }

  private function getNodeDate($xml, $xpath, $default) {
    $result = $this->getNode($xml, $xpath);
    if ($result) {
      return strtotime($result->__toString());
    } else return $default;
  }

  private function getNode($xml, $xpath) {
    $result = $this->getNodeList($xml,$xpath);
    if (count($result) > 0)
      return $result[0];  
    else return null;
  }

  private function getNodeList($xml, $xpath) {
    return $xml->xpath($xpath);
  }
}
?>