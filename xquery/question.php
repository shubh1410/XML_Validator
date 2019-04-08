<?php

/**
 * xquery question definition class.
 *
 * @package    qtype
 * @subpackage xquery
 */


defined('MOODLE_INTERNAL') || die();
include_once 'execute.php';

/**
 * Represents a xquery question.
 */
class qtype_xquery_question extends question_graded_by_strategy
        implements question_response_answer_comparer {
    /** @var boolean whether answers should be graded case-sensitively. */
    public $usecase;

    public $dbname;
    /** @var array of question_answer. */
    public $answers = array();

    public function __construct() {
        parent::__construct(new question_first_matching_answer_grading_strategy($this));
    }

    public function get_expected_data() {
        return array('answer' => PARAM_RAW_TRIMMED);
    }

    public function summarise_response(array $response) {
        if (isset($response['answer'])) {
            return $response['answer'];
        } else {
            return null;
        }
    }

    public function is_complete_response(array $response) {
        return array_key_exists('answer', $response) &&
                ($response['answer'] || $response['answer'] === '0');
    }

    public function get_validation_error(array $response) {
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseenterananswer', 'qtype_xquery');
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        return question_utils::arrays_same_at_key_missing_is_blank(
                $prevresponse, $newresponse, 'answer');
    }

    public function get_answers() {
        return $this->answers;
    }

    public function compare_response_with_answer(array $response, question_answer $answer) {

//        return self::compare_string_with_wildcard(
//                $response['answer'], $answer->answer, !$this->usecase);
        $eval = new evaluation_xquery();
        $result = $eval->evaluate_response($response['answer'], $answer->answer, $this->dbname);
        return $result;


    }

/**
 * This function "compare_string_with_wildcard()" is no longer required.
 */

//    public static function compare_string_with_wildcard($string, $pattern, $ignorecase) {
//        // Break the string on non-escaped asterisks.
//        $bits = preg_split('/(?<!\\\\)\*/', $pattern);
//        // Escape regexp special characters in the bits.
//        $excapedbits = array();
//        foreach ($bits as $bit) {
//            $excapedbits[] = preg_quote(str_replace('\*', '*', $bit));
//        }
//        // Put it back together to make the regexp.
//        $regexp = '|^' . implode('.*', $excapedbits) . '$|u';
//
//        // Make the match insensitive if requested to.
//        if ($ignorecase) {
//            $regexp .= 'i';
//        }
//
//        return preg_match($regexp, trim($string));
//    }

    public function get_correct_response() {
        $response = parent::get_correct_response();
        if ($response) {
            $response['answer'] = $this->clean_response($response['answer']);
        }
        return $response;
    }

    public function clean_response($answer) {
        // Break the string on non-escaped asterisks.
        $bits = preg_split('/(?<!\\\\)\*/', $answer);

        // Unescape *s in the bits.
        $cleanbits = array();
        foreach ($bits as $bit) {
            $cleanbits[] = str_replace('\*', '*', $bit);
        }

        // Put it back together with spaces to look nice.
        return trim(implode(' ', $cleanbits));
    }

    public function check_file_access($qa, $options, $component, $filearea,
            $args, $forcedownload) {
        if ($component == 'question' && $filearea == 'answerfeedback') {
            $currentanswer = $qa->get_last_qt_var('answer');
            $answer = $qa->get_question()->get_matching_answer(array('answer' => $currentanswer));
            $answerid = reset($args); // itemid is answer id.
            return $options->feedback && $answerid == $answer->id;

        } else if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);

        } else {
            return parent::check_file_access($qa, $options, $component, $filearea,
                    $args, $forcedownload);
        }
    }
}
