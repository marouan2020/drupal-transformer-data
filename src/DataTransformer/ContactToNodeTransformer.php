<?php
/**
 * Created by PhpStorm.
 * User: Ben Mansour Marouan
 * Date: 11/02/20
 * Time: 21:16
 */

namespace Drupal\accor_data\DataTransformer;

use Drupal\accor_data\Data\Contact;
use Drupal\node\Entity\Node;

/**
 * Class ContactToNodeTransformer.
 *
 * @package Drupal\accor_data\DataTransformer
 */
class ContactToNodeTransformer {

  /**
   * Covert contact to drupal entity node.
   *
   * @param Contact $contact
   *   contact class.
   *
   * @return \Drupal\Core\Entity\EntityInterface|mixed|null
   *   Can be drupal entity node.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function transform(Contact $contact) {
    $node = $this->getContactById($contact->getId());
    if (empty($node)) {
      $node = $this->createContact($contact->getFirstName(), $contact->getStatus());
    }
    if ($contact->hasFirstName()) {
      $node->setTitle($contact->getFirstName());
    }
    if ($contact->hasLastName()) {
      $node->set('field_last_name', $contact->getLastName());
    }
    $node->set('field_email', $contact->getEmail());
    $node->set('field_phone_number', $contact->getPhone());
    if ($contact->hasJobTitle()) {
      $jobTerm = $this->getJobTitleIdByName($contact->getJobTitle());
      $node->set('field_job_title', $jobTerm->id());
    }
    if ($contact->hasCountries()) {
      $countriesIds = $this->getCountriesContact($contact->getCountries());
      $node->set('field_country', $countriesIds);
    }
    if ($contact->hasImage()) {
      $node->set('field_image', $contact->getImage());
    }
    if ($contact->hasFamilyConcept()) {
      $node->set('field_category', $this->getFamiliesConceptByName($contact->getFamiliesConcept()));
    }
    // TODO $contact->getType()
    $node->set('field_id_a2s', $contact->getId());
    $node->save();
    return $node;
  }

  /**
   * Convert node to contact class.
   *
   * @param \Drupal\node\Entity\Node $node
   *  Drupal entity node.
   *
   * @return \Drupal\accor_data\Data\Contact
   *   Contact class.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function reverseTransform(Node $node) {
    $idA2s = $node->get('field_id_a2s')->first()->getString();
    $lastName = $node->get('field_last_name')->first()->getString();
    $contact = new Contact($idA2s, $node->label(), $lastName);
    $contact->setStatus($node->isPublished());
    if(!$node->get('field_email')->isEmpty()) {
      $contact->setEmail($node->get('field_email')->first()->getString());
    }
    if(!$node->get('field_phone_number')->isEmpty()) {
      $contact->setPhone($node->get('field_phone_number')->first()->getString());
    }

    // Put countries
    if (!$node->get('field_country')->isEmpty()) {
      $contactToTaxonomyTermTransformer = new CountryToTaxonomyTermTransformer();
      foreach ($node->get('field_country')->referencedEntities() as $country) {
        $country = $contactToTaxonomyTermTransformer->reverseTransform($country);
        $contact->addCountry($country);
      }
    }

    // Put image
    if(!$node->get('field_image')->isEmpty()) {
      /**
       * @var \Drupal\media\Entity\Media $mediaFieldImage
       */
      $mediaFieldImage = $node->get('field_image')->referencedEntities()[0];
      if(!empty($mediaFieldImage) && !$mediaFieldImage->get('field_media_image')->isEmpty()) {
        /**
         * @var \Drupal\file\Entity\File $fileFieldImage
         */
        $fileFieldImage = $mediaFieldImage->get('field_media_image')->referencedEntities()[0];
        $contact->setImage($fileFieldImage->url());
      }
    }

    // Put job title
    if (!$node->get('field_job_title')->isEmpty()) {
      $jobTitle = $node->get('field_job_title')->referencedEntities()[0];
      $contact->setJobTitle($jobTitle->getName());
    }

    // Put family/concept
    if (!$node->get('field_category')->isEmpty()) {
      foreach ($node->get('field_category')->referencedEntities() as $termJobTitle) {
        $contact->addFamilyConcept($termJobTitle->label());
      }
    }
    return $contact;
  }

  /**
   *  Retrieve contact by id a2s.
   *
   * @param int $idA2s
   *   Value id a2s.
   *
   * @return Node|null
   *   Can be Drupal entity node.
   */
  protected function getContactById($idA2s) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'team_member')
      ->condition('field_id_a2s', $idA2s);
    $nids = $query->execute();
    $nodes = node_load_multiple($nids);
    return !empty($nodes) ? reset($nodes) : null;
  }

  /**
   * Create new contact.
   *
   * @param $contactFirstName
   *   First name of contact.
   * @param $contactStatus
   *   Status of contact.
   *
   * @return \Drupal\Core\Entity\EntityInterface|Node
   *   Can be Drupal entity node.
   */
  protected function createContact($contactFirstName, $contactStatus) {
    $node = Node::create([
      'type' => 'team_member',
      'title' => $contactFirstName,
      'status' => $contactStatus,

    ]);
    return $node;
  }

  /**
   * Retrieve the job title term id by name.
   *
   * @param $name
   *   Name of job title.
   *
   * @return \Drupal\taxonomy\Entity\Term|string
   *  Can be a drupal entity taxonomy term.
   */
  protected function getJobTitleIdByName($name) {
    $terms = taxonomy_term_load_multiple_by_name($name, 'job_title');
    $term = reset($terms);
    return !empty($term) ? $term: '_none';
  }

  /**
   * Retrieve countries id by countries names.
   *
   * @param $countries
   *   List of names countries.
   *
   * @return array
   *   Can be a list of countries ids.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function getCountriesContact($countries) {
    $countriesIds = [];
    $countryToTaxonomyTermTransformer = new CountryToTaxonomyTermTransformer();
    foreach ($countries as $country) {
      $countryToTaxonomyTerm = $countryToTaxonomyTermTransformer->transform($country);
      $countriesIds[] = $countryToTaxonomyTerm->id();
    }
    return $countriesIds;
  }

  /**
   * Retrieve Family concept term.
   *
   * @param array $familiesConcept
   *   List of name term family concept.
   *
   * @return array
   *   Can be lis of id term family concept.
   */
  protected function getFamiliesConceptByName(array $familiesConcept) {
    $familiesConceptIds = [];
    foreach ($familiesConcept as $nameFamilyConcept) {
      $terms = taxonomy_term_load_multiple_by_name($nameFamilyConcept, 'category_level_2');
      $term = reset($terms);
      $familiesConceptIds[] = !empty($term) ? $term->id() : '_none';
    }
    return $familiesConceptIds;
  }
}
