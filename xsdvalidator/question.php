<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * xsdvalidator question definition class.
 *
 * @package   qtype_xsdvalidator
 * @copyright 2013 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/pmatch/pmatchlib.php');

/**
 * Represents a xsdvalidator question.
 *
 * @copyright 2013 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_xsdvalidator_question extends question_graded_automatically_with_countback {

    /** @var Standard partially correct fields. */
    public $correctfeedback;
    public $correctfeedbackformat;
    public $partiallycorrectfeedback;
    public $partiallycorrectfeedbackformat;
    public $incorrectfeedback;
    public $incorrectfeedbackformat;

    /**
     * @var array string => bool, the given sentences, and whether the
     * expression should match them or not.
     */
    public $sentences = array();

    /**
     * @var array string => int, the id of each sentence in the DB.
     */
    public $sentenceids = array();

    public function get_expected_data() {
        return array('answer' => PARAM_RAW_TRIMMED);
    }

    public function get_question_summary() {
        $bits = array();
        foreach ($this->sentences as $sentence => $shouldmatch) {
            if ($shouldmatch) {
                $bits[] = get_string('matchx', 'qtype_xsdvalidator', $sentence);
            } else {
                $bits[] = get_string('dontmatchx', 'qtype_xsdvalidator', $sentence);
            }
        }
        return implode('; ', $bits);
    }

    public function summarise_response(array $response) {
            return $response['answer'];

    }

    public function classify_response(array $response) {
        if (empty($response['answer'])) {
            return array(0 => question_classified_response::no_response());
        }

        $expression = $response['answer'];
        $numparts = count($this->sentences);

        $parts = array();
        foreach ($this->sentences as $sentence => $shouldmatch) {
            $doesmatch = $this->xml_validates_xsd($sentence, $expression);
            $parts[$this->sentenceids[$sentence]] = new question_classified_response($doesmatch, $response['answer'],
                    $this->compare_bools($shouldmatch, $doesmatch) / $numparts);
        }

        return $parts;
    }

    public function is_complete_response(array $response) {
        if (!array_key_exists('answer', $response)) {
            return false;
        }
        $expression = $response['answer'];
        $sxe=simplexml_load_string($expression);
        if(!$sxe)
          return false;

        return true;
    }

    public function is_gradable_response(array $response) {
        return array_key_exists('answer', $response) && $response['answer'] !== '';
    }

    public function get_validation_error(array $response) {
        return '';
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        return question_utils::arrays_same_at_key_missing_is_blank(
                $prevresponse, $newresponse, 'answer');
    }

    public function get_correct_response() {
        return array();
    }

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);

        } else if ($component == 'question' && in_array($filearea,
                array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'))) {
            return $this->check_combined_feedback_file_access($qa, $options, $filearea, $args);

        } else {
            return parent::check_file_access($qa, $options, $component, $filearea,
                    $args, $forcedownload);
        }
    }

    /**
     * @param string $currentanswer a response.
     * @return pmatch_expression the equivalent parsed expression.
     */
    public function parse_expression($currentanswer) {
        return $currentanswer;
    }

    /**
     * @param string $sentence a response.
     * @param pmatch_expression $expression a pmatch expression, not necessarily valid.
     * @return bool whether the sentence matches the pattern. If invalid, false is returned.
     */
    public function sentence_matches_expression($sentence, $expression) {
      $sxe=simplexml_load_string($expression);
      if(!$sxe)
        return false;

      return true;

    }

    public function xml_validates_xsd($sentence, $expression){
      try{

          $sxe = simplexml_load_string($sentence);
          if (!$sxe) {
           return 0;
          }
          $dom_sxe = dom_import_simplexml($sxe);
          $dom = new DOMDocument('1.0');
          $dom_sxe = $dom->importNode($dom_sxe, true);
          $dom_sxe = $dom->appendChild($dom_sxe);
          $dom->saveXML();

          $schema = $expression;

          if ($dom->schemaValidateSource($schema)) {
             return true;
          } else {
             return false;
          }
          }catch(Exception $e){
                return false;
              }
    }

    /**
     * @param bool $shouldmatch a bool.
     * @param bool $doesmatch another bool.
     * @return bool whether the two inputs are the same.
     */
    public function compare_bools($shouldmatch, $doesmatch) {
        return !($shouldmatch xor $doesmatch);
    }

    public function grade_response(array $response) {
        $expression = $response['answer'];

        $sxe=simplexml_load_string($expression);
        if(!sxe){
          return array(0,question_state::$gradedwrong);
        }

        $numright = 0;
        foreach ($this->sentences as $sentence => $shouldmatch) {
            if ($this->compare_bools($shouldmatch, $this->xml_validates_xsd($sentence, $expression))) {
                $numright += 1;
            }
        }
        $fraction = $numright / count($this->sentences);
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    public function compute_final_grade($responses, $totaltries) {
        $expressions = array();
        foreach ($responses as $i => $response) {
            $expressions[$i] = $response['answer'];
        }

        $totalscore = 0;
        foreach ($this->sentences as $sentence => $shouldmatch) {
            $lastwrongindex = -1;
            $finallyright = false;
            foreach ($expressions as $i => $expression) {
                if ($this->compare_bools($shouldmatch, $this->xml_validates_xsd($sentence, $expression))) {
                    $finallyright = true;
                } else {
                    $lastwrongindex = $i;
                    $finallyright = false;
                }
            }

            if ($finallyright) {
                $totalscore += max(0, 1 - ($lastwrongindex + 1) * $this->penalty);
            }
        }

        return $totalscore / count($this->sentences);
    }
}
