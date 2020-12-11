<?php
/**
 * Created by PhpStorm.
 * User: Ben Mansour Marouan
 * Date: 11/02/20
 * Time: 21:16
 */

namespace Drupal\import_data\Data;

/**
 * Class Contact.
 *
 * @package Drupal\import_data\Data
 */
class Contact {

  use Translations;

  /**
   * @var int
   */
  private $id;

  /**
   * @var string
   */
  private $lastName;

  /**
   * @var string
   */
  private $firstName;

  /**
   * @var string
   */
  private $jobTitle;

  /**
   * @var string
   */
  private $email;

  /**
   * @var string
   */
  private $phone;

  /**
   * @var string
   */
  private $type;

  /**
   * @var string[]
   */
  private $countries;

  /**
   * @var string
   */
  private $status;

  /**
   * @var string
   */
  private $image;

  /**
   * @var string[]
   */
  private $familiesConcept;

  /**
   * Contact constructor.
   *
   * @param int $id
   * @param string $lastName
   * @param string $firstName
   */
  public function __construct($id, $firstName, $lastName) {
    $this->id = $id;
    $this->firstName = $firstName;
    $this->lastName = $lastName;
  }


  /**
   * Get id contact
   *
   *@return int
   *  id of contact
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set id contact.
   *
   *@param int $id
   *  id of contact.
   *   
   *@return Contact
   *  Contact object id.
   */
  public function setId($id) {
    $this->id = $id;
    return $this;
  }

  /**
   * Check if contact has a last name.
   *
   * @return bool
   *  Can be true or false.
   */
  public function hasLastName() {
    return !empty($this->lastName);
  }

  /**
   * Get the last name of contact.
   *
   * @return string
   */
  public function getLastName() {
    return $this->lastName;
  }

  /**
   * Put last name to contact.
   *
   *@param string $lastName
   *
   *@return Contact
   */
  public function setLastName($lastName) {
    $this->lastName = $lastName;
    return $this;
  }

  /**
   * Check if contact has a first name.
   *
   *@return bool
   *    
   */
  public function hasFirstName() {
    return !empty($this->firstName);
  }

  /**
   * Get first name.
   *
   * @return string
   */
  public function getFirstName() {
    return $this->firstName;
  }

  /**
   *  Set first name.
   *
   * @param string $firstName
   *
   * @return Contact
   */
  public function setFirstName($firstName) {
    $this->firstName = $firstName;
    return $this;
  }

  /**
   * Check if contact has a job title.
   *
   * @return bool
   */
  public function hasJobTitle() {
    return !empty($this->jobTitle);
  }

  /**
   * @return string
   */
  public function getJobTitle() {
    return $this->jobTitle;
  }

  /**
   * @param string $jobTitle
   *
   * @return Contact
   */
  public function setJobTitle($jobTitle) {
    $this->jobTitle = $jobTitle;
    return $this;
  }

  /**
   * @return string
   */
  public function getEmail() {
    return $this->email;
  }

  /**
   * @param string $email
   *
   * @return Contact
   */
  public function setEmail($email) {
    $this->email = $email;
    return $this;
  }

  /**
   * @return string
   */
  public function getPhone() {
    return $this->phone;
  }

  /**
   * @param string $phone
   *
   * @return Contact
   */
  public function setPhone($phone) {
    $this->phone = $phone;
    return $this;
  }

  /**
   * @return string
   */
  public function getType() {
    return $this->type;
  }

  /**
   * @return bool
   */
  public function hasType() {
    return !empty($this->type);
  }

  /**
   * @param string $type
   *
   * @return Contact
   */
  public function setType($type) {
    $this->type = $type;
    return $this;
  }

  /**
   * Check if contact has countries.
   *
   * @return bool
   */
  public function hasCountries() {
    return !empty($this->countries);
  }


  /**
   * Gets countries contect.
   *
   * @return string[]
   */
  public function getCountries() {
    return $this->countries;
  }

  /**
   * Add country to contact.
   *
   * @param string $country
   *
   * @return Contact
   */
  public function addCountry($country) {
    $this->countries[] = $country;
    return $this;
  }

  /**
   * Get status.
   *
   * @return string
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * Set Status
   *
   * @param string $status
   *  Status.
   *
   * @return $this
   */
  public function setStatus($status) {
    $this->status = $status;
    return $this;
  }

  /**
   * @return string
   */
  public function getFullName() {
    return $this->firstName . ' ' . $this->lastName;
  }

  /**
   * @return bool
   */
  public function hasImage() {
    return !empty($this->image);
  }

  /**
   * @return string
   */
  public function getImage() {
    return $this->image;
  }

  /**
   * Set image
   *
   * @param string $image
   *  
   * @return $this
   */
  public function setImage($image) {
    $this->image = $image;
    return $this;
  }

  /**
   * Check if contact has countries.
   *
   * @return bool
   */
  public function hasFamilyConcept() {
    return !empty($this->familiesConcept);
  }

  /**
   * Get Family concept
   *  
   * @return $this
   */
  public function getFamiliesConcept() {
    return $this->familiesConcept;
  }


  /**
   * Add Family concept
   *  
   * @return $this
   */
  public function addFamilyConcept($familyConcept) {
    $this->familiesConcept[] = $familyConcept;
    return $this;
  }
}
