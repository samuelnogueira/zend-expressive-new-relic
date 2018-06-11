<?php namespace Samuelnogueira\ZendExpressiveNewRelic;

class NewRelicAgentFactory
{
    public function __invoke(): NewRelicAgent
    {
        return new NewRelicAgent();
    }
}
