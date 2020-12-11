<?php

namespace Drupal\accor_data\Data;

Trait Translations {

  /**
   * @var Category[]
   */
  private $translations;

  /**
   * @var string
   */
  private $language;

  /**
   * @param string $language
   *
   * @return Category
   */
  public function getTranslation($language) {
    return $this->translations[$language] ?? null;
  }

  public function addTranslation(Category $category) {
    $this->translations[$category->getLanguage()] = $category;
  }

  /**
   * @return string
   */
  public function getLanguage() {
    return $this->language;
  }

  /**
   * @param $language
   *
   * @return $this
   */
  public function setLanguage($language) {
    $this->language = $language;
    return $this;
  }

}
