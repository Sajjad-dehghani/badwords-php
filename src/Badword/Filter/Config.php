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
 * Config defines settings and regular expression generation
 * rules the Filter adheres to when executing.
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
        return $this->addRuleToStack($rule, $this->rules);
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
        return $this->addRulesToStack($rules, $this->rules);
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
        return $this->setRulesToStack($rules, $this->rules);
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
        return $this->addRuleToStack($rule, $this->preRules);
    }

    /**
     * Adds "pre" Rules (executed before the standard Rules).
     *
     * @param array $rules
     *
     * @return Config
     *
     * @throws \InvalidArgumentException When a rule is invalid.
     */
    public function addPreRules(array $rules)
    {
        return $this->addRulesToStack($rules, $this->preRules);
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
     * @throws \InvalidArgumentException When a rule is invalid.
     */
    public function setPreRules(array $rules)
    {
        return $this->setRulesToStack($rules, $this->preRules);
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
        return $this->addRuleToStack($rule, $this->postRules);
    }

    /**
     * Adds "post" Rules (executed after the standard Rules).
     *
     * @param array $rules
     *
     * @return Config
     *
     * @throws \InvalidArgumentException When a rule is invalid.
     */
    public function addPostRules(array $rules)
    {
        return $this->addRulesToStack($rules, $this->postRules);
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
     * @throws \InvalidArgumentException When a rule is invalid.
     */
    public function setPostRules(array $rules)
    {
        return $this->setRulesToStack($rules, $this->postRules);
    }

    /**
     * Adds a Rule to the specified stack.
     *
     * @param Rule $rule
     * @param array &$stack
     *
     * @return Config
     */
    protected function addRuleToStack(Rule $rule, array &$stack)
    {
        if(!in_array($rule, $stack))
        {
            array_push($stack, $rule);
        }

        return $this;
    }

    /**
     * Adds Rules to the specified stack.
     *
     * @param array $rules
     * @param array &$stack
     *
     * @return Config
     *
     * @throws \InvalidArgumentException When a rule is invalid.
     */
    protected function addRulesToStack(array $rules, array &$stack)
    {
        foreach($rules as $key => $rule)
        {
            if(!($rule instanceof Rule))
            {
                throw new \InvalidArgumentException(sprintf('Invalid rule at key "%s". Expected instance of \Badword\Filter\Config\Rule.', $key));
            }
        }

        foreach($rules as $rule)
        {
            $this->addRuleToStack($rule, $stack);
        }

        return $this;
    }

    /**
     * Sets the Rules for the specified stack.
     *
     * @param array $rules
     * @param array &$stack
     *
     * @return Config
     *
     * @throws \InvalidArgumentException When a rule is invalid.
     */
    protected function setRulesToStack(array $rules, array &$stack)
    {
        foreach($rules as $key => $rule)
        {
            if(!($rule instanceof Rule))
            {
                throw new \InvalidArgumentException(sprintf('Invalid rule at key "%s". Expected instance of \Badword\Filter\Config\Rule.', $key));
            }
        }

        $stack = array();

        foreach($rules as $rule)
        {
            $this->addRuleToStack($rule, $stack);
        }

        return $this;
    }

    /**
     * Applies the regular expression generation Rules to the Word.
     *
     * @param Word $word
     * 
     * @return string Generated regular expression.
     */
    public function applyRulesToWord(Word $word)
    {
        $regExp = addslashes($word->getWord());
        $rules = array_merge($this->getPreRules(), $this->getRules(), $this->getPostRules());

        foreach($rules as $rule)
        {
            $regExp = $rule->apply($regExp, $word);
        }

        return $regExp;
    }
}