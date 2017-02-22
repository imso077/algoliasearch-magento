<?php

class Algolia_Algoliasearch_Model_System_Config_Backend_ExtraSettings extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $value = trim($this->getValue());

        if (empty($value)) {
            return parent::_beforeSave();
        }

        $fieldName = $this->getField();

        $fieldConfig = $this->getFieldConfig();
        $label = (string) $fieldConfig->label;

        $value = json_decode($value);
        $error = json_last_error();

        if ($error) {
            Mage::throwException('JSON provided for "'.$label.'" field is not valid JSON.');
        }

        $indexName = 'temp_'.$fieldName.'_'.md5($fieldName);

        /** @var Algolia_Algoliasearch_Helper_Algoliahelper $algoliaHelper */
        $algoliaHelper = Mage::helper('algoliasearch/algoliahelper');

        try {
            $algoliaHelper->getIndex($indexName)->setSettings($value);
        } catch (\Exception $e) {
            $algoliaHelper->deleteIndex($indexName);
            Mage::throwException('Settings provided in "'.$label.'" are not valid Algolia settings - '.$e->getMessage());
        }

        $algoliaHelper->deleteIndex($indexName);

        return parent::_beforeSave();
    }
}
