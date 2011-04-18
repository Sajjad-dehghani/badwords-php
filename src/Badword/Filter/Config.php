<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badword\Filter;

use Badword\Filter\Config\Rule;
use Badword\Word;

/**
 * Config defines the rules and settings the Filter adheres to when executing.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Config
{
    /**
     * @var array
     */
    protected $rules = array();

    /**
     * @var array
     */
    protected $preRules = array();

    /**
     * @var array
     */
    protected $postRules = array();

    /**
     * Constructs a new Config.
     * 
     * @param array $rules The Rules.
     * @param array $preRules The "pre" Rules (executed before the standard Rules).
     * @param array $postRules The "post" Rules (executed after the standard Rules).
     */
    public function __construct(array $rules = array(), array $preRules = array(), array $postRules = array())
    {
        $this->setRules($rules);
        $this->setPreRules($preRules);
        $this->setPostRules($postRules);
    }

    /**
     * Adds a Rule.
     * 
     * @param Rule $rule
     * 
     * @return Config
     */
    public function addRule(Rule $rule)
    {
        if(!in_array($rule, $this->getRules()))
        {
            array_push($this->rules, $rule);
        }

        return $this;
    }

    /**
     * Adds Rules.
     *
     * @param array $rules
     *
     * @return Config
     *
     * @throws \InvalidArgumentException When a rule is invalid.
     */
    public function addRules(array $rules)
    {
        foreach($rules as $rule)
        {
            if(!($rule instanceof Rule))
            {
                throw new \InvalidArgumentException('Invalid rule. Expected instance of \Badword\Filter\Config\Rule.');
            }
        }

        foreach($rules as $rule)
        {
            $this->addRule($rule);
        }

        return $this;
    }

    /**
     * Gets the Rules.
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Sets the Rules.
     *
     * @param array $rules
     *
     * @return Config
     *
     * @throws \InvalidArgumentException When a rule is invalid.
     */
    public function setRules(array $rules)
    {
        foreach($rules as $rule)
        {
            if(!($rule instanceof Rule))
            {
                throw new \InvalidArgumentException('Invalid rule. Expected instance of \Badword\Filter\Config\Rule.');
            }
        }

        $this->rules = array();

        foreach($rules as $rule)
        {
            $this->addRule($rule);
        }

        return $this;
    }

    /**
     * Adds a "pre" Rule (executed before the standard Rules).
     *
     * @param Rule $rule
     *
     * @return Config
     */
    public function addPreRule(Rule $rule)
    {
        if(!in_array($rule, $this->getPreRules()))
        {
            array_push($this->preRules, $rule);
        }

        return $this;
    }

    /**
     * Adds "pre" Rules (executed before the standard Rules).
     *
     * @param array $rules
     *
     * @return Config
     *
     * @throws \InvalidArgumentException When a "pre" rule is invalid.
     */
    public function addPreRules(array $rules)
    {
        foreach($rules as $rule)
        {
            if(!($rule instanceof Rule))
            {
                throw new \InvalidArgumentException('Invalid "pre" rule. Expected instance of \Badword\Filter\Config\Rule.');
            }
        }

        foreach($rules as $rule)
        {
            $this->addPreRule($rule);
        }

        return $this;
    }

    /**
     * Gets the "pre" Rules (executed before the standard Rules).
     *
     * @return array
     */
    public function getPreRules()
    {
        return $this->preRules;
    }

    /**
     * Sets the "pre" Rules (executed before the standard Rules).
     *
     * @param array $rules
     *
     * @return Config
     *
     * @throws \InvalidArgumentException When a "pre" rule is invalid.
     */
    public function setPreRules(array $rules)
    {
        foreach($rules as $rule)
        {
            if(!($rule instanceof Rule))
            {
                throw new \InvalidArgumentException('Invalid "pre" rule. Expected instance of \Badword\Filter\Config\Rule.');
            }
        }

        $this->preRules = array();

        foreach($rules as $rule)
        {
            $this->addPreRule($rule);
        }

        return $this;
    }

    /**
     * Adds a "post" Rule (executed after the standard Rules).
     *
     * @param Rule $rule
     *
     * @return Config
     */
    public function addPostRule(Rule $rule)
    {
        if(!in_array($rule, $this->getPostRules()))
        {
            array_push($this->postRules, $rule);
        }

        return $this;
    }

    /**
     * Adds "post" Rules (executed after the standard Rules).
     *
     * @param array $rules
     *
     * @return Config
     *
     * @throws \InvalidArgumentException When a "post" rule is invalid.
     */
    public function addPostRules(array $rules)
    {
        foreach($rules as $rule)
        {
            if(!($rule instanceof Rule))
            {
                throw new \InvalidArgumentException('Invalid "post" rule. Expected instance of \Badword\Filter\Config\Rule.');
            }
        }

        foreach($rules as $rule)
        {
            $this->addPostRule($rule);
        }

        return $this;
    }

    /**
     * Gets the "post" Rules (executed after the standard Rules).
     *
     * @return array
     */
    public function getPostRules()
    {
        return $this->postRules;
    }

    /**
     * Sets the "post" Rules (executed after the standard Rules).
     *
     * @param array $rules
     *
     * @return Config
     *
     * @throws \InvalidArgumentException When a "post" rule is invalid.
     */
    public function setPostRules(array $rules)
    {
        foreach($rules as $rule)
        {
            if(!($rule instanceof Rule))
            {
                throw new \InvalidArgumentException('Invalid "post" rule. Expected instance of \Badword\Filter\Config\Rule.');
            }
        }

        $this->postRules = array();

        foreach($rules as $rule)
        {
            $this->addPostRule($rule);
        }

        return $this;
    }

    /**
     * Applies the Config to the Word.
     *
     * @param Word $word
     *
     * @return string Generated regular expression.
     */
    public function apply(Word $word)
    {
        $data = $word->getWord();
        $rules = array_merge($this->getPreRules(), $this->getRules(), $this->getPostRules());

        foreach($rules as $rule)
        {
            $data = $rule->apply($data, $word);
        }

        return $data;
    }
}