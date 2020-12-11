<?php
/**
 * Created by PhpStorm.
 * User: Ben Mansour Marouan
 * Date: 11/02/20
 * Time: 21:16
 */

namespace Drupal\accor_data\Validator;

use Drupal\accor_data\Data\Contact;
use Symfony\Component\Intl\Countries;

/**
 * Class ContactValidator
 *
 * @package Drupal\accor_data\Validator
 */
class ContactValidator
{

  /**
   * Validate required field after import process.
   *
   *@param Contact $contact
   *  Contact transformer object.
   *
   *@return array
   *  Can be a list of errors detected. 
   */
  public function validate(Contact $contact) {
    $errors = [];
    if (empty($contact->getId())) {
      $errors[] = "Contact id can't be empty";
    }
    if (empty($contact->getFirstName())) {
      $errors[] = "First name can't be empty";
    }
    if (empty($contact->getLastName())) {
      $errors[] = "Last name can't be empty";
    }
    if ($contact->hasCountries() && !empty($this->getInvalidContactCountries($contact->getCountries()))) {
      $invalidCountries = implode(",", $this->getInvalidContactCountries($contact->getCountries()));
      $errors[] = "They countries iso code ({$invalidCountries}) are not valid.";
    }
    if ($contact->hasJobTitle() && !$this->existJobTile($contact->getJobTitle())) {
      $errors[] = "Job title not found";
    }
    if (empty($contact->getStatus())) {
      $errors[] = "Status is missing";
    }
    if (!empty($contact->getEmail()) && !\Drupal::service('email.validator')->isValid($contact->getEmail())) {
      $errors[] = "Contact email address is not a valid one.";
    }
    return $errors;
  }

  /**
   * Gets invalid countries code.
   *
   * @param array $countriesCode
   *   List of countries code.
   *
   * @return array
   *   List of invalid country code
   */
  private function getInvalidContactCountries(array $countriesCode) {
    $invalidIsoCodes = [];
    foreach ($countriesCode as $code) {
      if (!Countries::exists($code)) {
        $invalidIsoCodes[] = $code;
      }
    }
    return $invalidIsoCodes;
  }

  /**
   * Check if exist job title.
   *
   * @param string $jobTitle
   *   Name of job title term.
   *
   * @return bool
   *   can be true or false.
   */
  public function existJobTile($jobTitle) {
    $jobTitleTerm =  taxonomy_term_load_multiple_by_name($jobTitle, 'job_title');
    if (!empty($jobTitleTerm)) {
      return TRUE;
    }
    return FALSE;
  }
}
