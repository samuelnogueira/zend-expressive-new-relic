<?php

namespace Samuelnogueira\ZendExpressiveNewRelic;

/** @final */
class NewRelicAgentFactory
{
    public function __invoke(): NewRelicAgent
    {
        return new NewRelicAgent();
    }
}
