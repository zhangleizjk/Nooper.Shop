<?php

// declare(strict_types = 1);
namespace Nooper;

use DOMDocument;
use DOMElement;

class Translator {
	
	/**
	 * public ?string function create_json(array $datas)
	 */
	public function create_json(array $datas): string {
		$json = json_encode($datas, JSON_UNESCAPED_UNICODE);
		return json_last_error() != JSON_ERROR_NONE ? $json : null;
	}
	
	/**
	 * public ?array parse_json(string $json)
	 */
	public function parse_json(string $json): ?array {
		$datas = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);
		return json_last_error() == JSON_ERROR_NONE ? $datas : null;
	}
	
	/**
	 * public ?string function create_xml(array $datas, DOMDocument $doc = null, DOMElement $root = null, boolean $cdata = true, boolean $doctype = false)
	 */
	public function create_xml(array $datas, DOMDocument $doc = null, DOMElement $root = null, bool $cdata = true, bool $doctype = false): string {
		if(is_null($doc)) $doc = new DOMDocument('1.0', 'utf-8');
		if(is_null($root)){
			$root = $doc->createElement('xml');
			$doc->appendChild($root);
		}
		foreach($datas as $key => $data){
			$child = $doc->createElement(is_string($key) ? $key : 'node');
			$root->appendChild($child);
			if(is_array($data)) $this->create_xml($data, $doc, $child, $cdata, $doctype);
			else{
				if(is_string($data)) $data = trim($data);
				elseif(is_numeric($data)) $data = (string)$data;
				elseif(is_bool($data)) $data = $data ? 'true' : 'false';
				elseif(is_null($data)) $data = '';
				elseif(is_object($data)) $data = get_class($data);
				elseif(is_resource($data)) $data = get_resource_type($data);
				else $data = '';
				$end = $cdata ? $doc->createCDATASection($data) : $doc->createTextNode($data);
				$child->appendChild($end);
			}
		}
		$xml = $doctype ? $doc->saveXML() : $doc->saveXML($root);
		return is_string($xml) ? $xml : null;
	}
	
	/**
	 * public ?array function parse_xml(string $xml, boolean $root = false)
	 */
	public function parse_xml(string $xml, bool $root = false): array {
		if($root) $xml = '<xml>' . $xml . '</xml>';
		$doctype = '<?xml version="1.0" encoding="utf-8"?>';
		$xml = $doctype . $xml;
		$doc = new DOMDocument('1.0', 'utf-8');
		if($doc->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOERROR)){
			$node = $doc->documentElement;
			$children = $node->childNodes;
			$yesNodeTypes = [XML_TEXT_NODE, XML_CDATA_SECTION_NODE, XML_ELEMENT_NODE];
			$yesEndNodeTypes = [XML_TEXT_NODE, XML_CDATA_SECTION_NODE];
			foreach($children as $child){
				if(!in_array($child->nodeType, $yesNodeTypes, true)) $node->removeChild($child);
			}
			$length = $children->length;
			if(0 == $length) $datas = [];
			elseif(1 == $length && in_array($children->item(0)->nodeType, $yesEndNodeTypes, true)) $datas[] = $child->wholeText;
			else{
				$datas = [];
				foreach($children as $child){
					if(in_array($child->nodeType, $yesEndNodeTypes, true)){
						$datas[] = $child->wholeText;
					}else{
						if('node' == $child->nodeName) $datas[] = $this->parse_xml($doc->saveXML($child));
						else $datas[$child->nodeName] = $this->parse_xml($doc->saveXML($child));
					}
				}
			}
		}
		return $datas ?? null;
	}
	// -- END --
}

