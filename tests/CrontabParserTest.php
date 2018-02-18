<?php

/**
 * Class CrontabParserTest
 */
class CrontabParserTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var $Parser exporter\parser\parser
     */
    private $Parser;


    public function setUp()
    {
        $this->Parser = new exporter\parser\parser();
    }

    private function AssertArrayCountTrue(array $array, $count, $elementtotest): void
    {
        if (count($array) === $count) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false, $elementtotest);
        }
    }

    private function AssertArrayCountFalse(array $array, $elementtotest): void
    {
        if (count($array) > 1) {
            $this->assertFalse(true, $elementtotest);
        } else {
            $this->assertFalse(false);
        }
    }

    /**
     * @group element
     */
    public function testCrontabElementMinutesTrue(): void
    {
        /** @noinspection ForeachSourceInspection */
        foreach ($this->ElementsToTest['min']['true'] as $elementtotest) {
            $matches = $this->CheckElement($elementtotest, \exporter\regex::$regexmin);
            $this->AssertArrayCountTrue($matches, 2, $elementtotest);
        }
    }

    /**
     * @group element
     */
    public function testCrontabElementMinutesFalse(): void
    {
        /** @noinspection ForeachSourceInspection */
        /** @noinspection ForeachSourceInspection */
        foreach ($this->ElementsToTest['min']['false'] as $elementtotest) {
            $matches = $this->CheckElement($elementtotest, \exporter\regex::$regexmin);
            $this->AssertArrayCountFalse($matches, $elementtotest);
        }
    }

    /**
     * @group element
     */
    public function testCrontabElementHoursTrue(): void
    {
        /** @noinspection ForeachSourceInspection */
        foreach ($this->ElementsToTest['hrs']['true'] as $elementtotest) {
            $matches = $this->CheckElement($elementtotest, \exporter\regex::$regexhrs);
            $this->AssertArrayCountTrue($matches, 2, $elementtotest);
        }
    }

    /**
     * @group element
     */
    public function testCrontabElementHoursFalse(): void
    {
        /** @noinspection ForeachSourceInspection */
        foreach ($this->ElementsToTest['hrs']['false'] as $elementtotest) {
            $matches = $this->CheckElement($elementtotest, \exporter\regex::$regexhrs);
            $this->AssertArrayCountFalse($matches, $elementtotest);
        }
    }

    /**
     * @group element
     */
    public function testCrontabElementDomTrue(): void
    {
        /** @noinspection ForeachSourceInspection */
        foreach ($this->ElementsToTest['dom']['true'] as $elementtotest) {
            $matches = $this->CheckElement($elementtotest, \exporter\regex::$regexdom);
            $this->AssertArrayCountTrue($matches, 2, $elementtotest);
        }
    }

    /**
     * @group element
     */
    public function testCrontabElementDomFalse(): void
    {
        /** @noinspection ForeachSourceInspection */
        foreach ($this->ElementsToTest['dom']['false'] as $elementtotest) {
            $matches = $this->CheckElement($elementtotest, \exporter\regex::$regexdom);
            $this->AssertArrayCountFalse($matches, $elementtotest);
        }
    }

    /**
     * @group element
     */
    public function testCrontabElementMonTrue(): void
    {
        /** @noinspection ForeachSourceInspection */
        foreach ($this->ElementsToTest['mon']['true'] as $elementtotest) {
            $matches = $this->CheckElement($elementtotest, \exporter\regex::$regexmon);
            $this->AssertArrayCountTrue($matches, 2, $elementtotest);
        }
    }

    /**
     * @group element
     */
    public function testCrontabElementMonFalse(): void
    {
        /** @noinspection ForeachSourceInspection */
        foreach ($this->ElementsToTest['mon']['false'] as $elementtotest) {
            $matches = $this->CheckElement($elementtotest, \exporter\regex::$regexmon);
            $this->AssertArrayCountFalse($matches, $elementtotest);
        }
    }

    /**
     * @group element
     */
    public function testCrontabElementDowTrue(): void
    {
        /** @noinspection ForeachSourceInspection */
        foreach ($this->ElementsToTest['dow']['true'] as $elementtotest) {
            $matches = $this->CheckElement($elementtotest, \exporter\regex::$regexdow);
            $this->AssertArrayCountTrue($matches, 2, $elementtotest);
        }
    }

    /**
     * @group element
     */
    public function testCrontabElementDowFalse(): void
    {
        /** @noinspection ForeachSourceInspection */
        foreach ($this->ElementsToTest['dow']['false'] as $elementtotest) {
            $matches = $this->CheckElement($elementtotest, \exporter\regex::$regexdow);
            $this->AssertArrayCountFalse($matches, $elementtotest);
        }
    }

    /**
     *
     */
    public function testCrontabLinesTrue(): void
    {
        /** @noinspection ForeachSourceInspection */
        foreach ($this->LinesToTest['true'] as $line) {
            $this->assertTrue($this->Parser->parseLine($line)['state'], $line);
        }
    }

    /**
     *
     */
    public function testCrontabLinesFalse(): void
    {
        /** @noinspection ForeachSourceInspection */
        foreach ($this->LinesToTest['false'] as $line) {
            $this->assertFalse($this->Parser->parseLine($line)['state'], $line);
        }
    }

    private /** @noinspection PropertyCanBeStaticInspection */
        $ElementsToTest = array(
        'min' => array(
            'true'  => array(
                '0-5',
                '1,3,5',
                '*',
                '*/15',
                '*/0',
                '*/0059',
            ),
            'false' => array(
                '69',
                'a',
                '123/*'
            )
        ),
        'hrs' => array(
            'true'  => array(
                '0-5',
                '1,3,5',
                '*',
                '*/099'
            ),
            'false' => array(
                '99',
                'a',
                '123/*'
            )
        ),
        'dom' => array(
            'true'  => array(
                '0-5',
                '1,3,5',
                '*',
                '*/099'
            ),
            'false' => array(
                '99',
                'a',
                '123/*'
            )
        ),
        'mon' => array(
            'true'  => array(
                '0-5',
                '1,3,5',
                '*',
                '*/099'
            ),
            'false' => array(
                '99',
                'a',
                '123/*'
            )
        ),
        'dow' => array(
            'true'  => array(
                '0-5',
                '1,3,5',
                '*',
                '*/5',
                '*/099'
            ),
            'false' => array(
                '99',
                'a',
                '123/*'
            )
        )
    );


    /**
     * @var array
     */
    private $LinesToTest = array(
        'true'  => array(
            // '33 08 10 06 * /home/ramesh/full-backup'
            '12-12 11,12,12 11,2,3 12,12,12 1,2,3 /home/ramesh/bin/incremental-backup',
            '00     09-18 *      * 1-5 /home/ramesh/bin/check-db-status', //tabs
            '00 09-18 * * * /home/ramesh/bin/check-db-status',
            '00 09-18 * * 1-5 /home/ramesh/bin/check-db-status',
            '00     09-18 *      * 1-5 /home/ramesh/bin/check-db-statusn && echo "  test  "', //tabs
            '00    09-18 *      * 1-5 /home/ramesh/bin/check-db-status', //tabs
            '10 * * * * /home/ramesh/check-disk-space',
            '* * * * * CMDramesh',
            '* * * * * CMDramesh #test1234',
            '##*/5 * * * * sh /root/test2'
        ),
        'false' => array(
            '00 09-18 * *  /home/ramesh/bin/check-db-status',
            '*/10 a * * * /home/ramesh/check-disk-space',
            '* * * * * ',
        )
    );

    /**
     * @param $elementtotest
     * @param $regex
     * @return array
     * @internal param $elementtype
     * @internal param $elementmin
     */
    private Function CheckElement($elementtotest, $regex): array
    {
        if ($regex && preg_match('/^(' . $regex . ')$/', $elementtotest, $matches)) {
            return $matches;
        }

        return array();
    }

    /**
     * @var array
     */
    private $LinesToTestinactive = array(
        'true'  => array(
            '#123# * * * * * CMDramesh',
            '## tmp deaktiviert ## * * * * * echo "test"'
        ),
        'false' => array(
            '#tmp deaktiviert#',
            '##34254## * * '
        )
    );

    private $LinesToTestWithComment = array(
        'true'  => array(
            '* * * * * CMDramesh #123',
            '* * * * * CMDramesh ### 123'
        ),
        'false' => array(
            '##34254## * * ',
            '* * * * * CMDramesh'
        )
    );

    private $LinesToTestInactiveWithComment = array(
        'true'  => array(
            '#123# * * * * * CMDramesh #123',
            '### 13444 ##* * * * * CMDramesh ### 123'
        ),
        'false' => array(
            '##34254## * * ',
            '* * * * * CMDramesh'
        )
    );

    /**
     *
     */
    public function testCrontabLinesInactiveTrue(): void
    {
        /** @noinspection ForeachSourceInspection */
        foreach ($this->LinesToTestinactive['true'] as $line) {
            $parseLineInactive = $this->Parser->parseLine($line);
            $this->assertEquals($parseLineInactive['job'], exporter\parser\parser::JOB_INACTIVE_WITHOUT_COMMENT);
        }
    }

    /**
     *
     */
    public function testCrontabLinesInactiveFalse(): void
    {
        /** @noinspection ForeachSourceInspection */
        /** @noinspection ForeachSourceInspection */
        foreach ($this->LinesToTestinactive['false'] as $line) {
            $parseLineInactive = $this->Parser->parseLine($line);
            $this->assertNotEquals($parseLineInactive['job'], exporter\parser\parser::JOB_INACTIVE_WITHOUT_COMMENT);
        }
    }

    /**
     *
     */
    public function testCrontabLinesWithCommentTrue(): void
    {
        /** @noinspection ForeachSourceInspection */
        foreach ($this->LinesToTestWithComment['true'] as $line) {
            $parseLineWithComment = $this->Parser->parseLine($line);
            $this->assertEquals($parseLineWithComment['job'], exporter\parser\parser::JOB_WITH_COMMENT);
        }
    }

    /**
     *
     */
    public function testCrontabLinesWithCommentFalse(): void
    {
        /** @noinspection ForeachSourceInspection */
        foreach ($this->LinesToTestWithComment['false'] as $line) {
            $parseLineWithComment = $this->Parser->parseLine($line);
            $this->assertNotEquals($parseLineWithComment['job'], exporter\parser\parser::JOB_WITH_COMMENT);
        }
    }

    /**
     *
     */
    public function testCrontabLinesInactiveWithCommentTrue(): void
    {
        /** @noinspection ForeachSourceInspection */
        foreach ($this->LinesToTestInactiveWithComment['true'] as $line) {
            $parseLineInactiveWithComments = $this->Parser->parseLine($line);
            $this->assertEquals($parseLineInactiveWithComments['job'], exporter\parser\parser::JOB_INACTIVE_WITH_COMMENT);
        }
    }

    /**
     *
     */
    public function testCrontabLinesInactiveWithCommentFalse(): void
    {
        /** @noinspection ForeachSourceInspection */
        foreach ($this->LinesToTestInactiveWithComment['false'] as $line) {
            $parseLineInactiveWithComments = $this->Parser->parseLine($line);
            $this->assertNotEquals($parseLineInactiveWithComments['job'], exporter\parser\parser::JOB_INACTIVE_WITH_COMMENT);
        }
    }
}
