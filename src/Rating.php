<?php

/**
 * This class calculates ratings based on the Elo system used in chess.
 *
 * @author Michal Chovanec <michalchovaneceu@gmail.com>
 * @copyright Copyright © 2012 - 2014 Michal Chovanec
 * @license Creative Commons Attribution 4.0 International License
 */

namespace Ferranfg\Elo;

class Rating
{
    /**
     * @var int The K Factor used.
     */
    protected $_kFactor;

    const WIN = 1;
    const DRAW = 0.5;
    const LOST = 0;

    /**
     * Protected & private variables.
     */
    protected $_ratingA;
    protected $_ratingB;

    protected $_scoreA;
    protected $_scoreB;

    protected $_expectedA;
    protected $_expectedB;

    protected $_diffRatingA;
    protected $_diffRatingB;

    protected $_newRatingA;
    protected $_newRatingB;

    /**
     * Constructor function which does all the maths and stores the results ready
     * for retrieval.
     *
     * @param int $ratingA Current rating of A
     * @param int $ratingB Current rating of B
     * @param int $scoreA Score of A
     * @param int $scoreB Score of B
     */
    public function __construct($ratingA, $ratingB, $scoreA, $scoreB, $kFactor = 16)
    {
        $this->_kFactor = $kFactor;

        $this->setNewSettings($ratingA, $ratingB, $scoreA, $scoreB);
    }

    /**
     * Set new input data.
     *
     * @param int $ratingA Current rating of A
     * @param int $ratingB Current rating of B
     * @param int $scoreA Score of A
     * @param int $scoreB Score of B
     * @return self
     */
    public function setNewSettings($ratingA, $ratingB, $scoreA, $scoreB)
    {
        $this->_ratingA = $ratingA;
        $this->_ratingB = $ratingB;
        $this->_scoreA = $scoreA;
        $this->_scoreB = $scoreB;

        $expectedScores = $this->_getExpectedScores($this->_ratingA, $this->_ratingB);
        $this->_expectedA = $expectedScores['a'];
        $this->_expectedB = $expectedScores['b'];

        $diffRatings = $this->_getDiffRatings($this->_expectedA, $this->_expectedB, $this->_scoreA, $this->_scoreB);
        $this->_diffRatingA = $diffRatings['a'];
        $this->_diffRatingB = $diffRatings['b'];

        $newRatings = $this ->_getNewRatings($this->_ratingA, $this->_ratingB, $this->_expectedA, $this->_expectedB, $this->_scoreA, $this->_scoreB);
        $this->_newRatingA = $newRatings['a'];
        $this->_newRatingB = $newRatings['b'];

        return $this;
    }

    /**
     * Retrieve the calculated data.
     *
     * @return array An array containing the new ratings for A and B.
     */
    public function getNewRatings()
    {
        return array (
            'a' => $this->_newRatingA,
            'b' => $this->_newRatingB
        );
    }

    /**
     * Retrieve the calculated data.
     *
     * @return array An array containing the diff ratings for A and B.
     */
    public function getDiffRatings()
    {
        return array (
            'a' => $this->_diffRatingA,
            'b' => $this->_diffRatingB
        );
    }

    // Protected & private functions begin here

    /**
     * @param int $ratingA The Rating of Player A
     * @param int $ratingB The Rating of Player B
     * @return array
     */
    protected function _getExpectedScores($ratingA, $ratingB)
    {
        $expectedScoreA = 1 / ( 1 + ( pow( 10 , ( $ratingB - $ratingA ) / 400 ) ) );
        $expectedScoreB = 1 / ( 1 + ( pow( 10 , ( $ratingA - $ratingB ) / 400 ) ) );

        return array (
            'a' => $expectedScoreA,
            'b' => $expectedScoreB
        );
    }

    /**
     * @param int $expectedA The expected score of Player A
     * @param int $expectedB The expected score of Player B
     * @param int $scoreA The score of Player A
     * @param int $scoreB The score of Player B
     * @return array
     */
    protected function _getDiffRatings($expectedA, $expectedB, $scoreA, $scoreB)
    {
        $diffRatingA = $this->_kFactor * ( $scoreA - $expectedA );
        $diffRatingB = $this->_kFactor * ( $scoreB - $expectedB );

        return array (
            'a' => $diffRatingA,
            'b' => $diffRatingB
        );
    }

    /**
     * @param int $ratingA The Rating of Player A
     * @param int $ratingB The Rating of Player A
     * @param int $expectedA The expected score of Player A
     * @param int $expectedB The expected score of Player B
     * @param int $scoreA The score of Player A
     * @param int $scoreB The score of Player B
     * @return array
     */
    protected function _getNewRatings($ratingA, $ratingB, $expectedA, $expectedB, $scoreA, $scoreB)
    {
        $diffRatings = $this->_getDiffRatings($expectedA, $expectedB, $scoreA, $scoreB);

        $newRatingA = $ratingA + $diffRatings['a'];
        $newRatingB = $ratingB + $diffRatings['b'];

        return array (
            'a' => $newRatingA,
            'b' => $newRatingB
        );
    }

}
