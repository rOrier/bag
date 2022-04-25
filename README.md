# bag
Storage object for complex data.

# Howto init Parameters

    $data = []

    $delayedParameters = new ParametersProxy();
    
    $parametersAnalyzer = new Analyzer([
        new ConstantParser(),
        new EnvParser(),
        new StringParameterParser($delayedParameters),
        new ArrayParameterParser($delayedParameters)
    ]);
    
    $parameters = new Parameters($data, $parametersAnalyzer);
    
    $delayedParameters->setParameters($parameters);
