<?php

namespace MVF\Servicer\Commands;

use MVF\Servicer\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExecCommand extends BaseCommand
{
    const ACTION = 'action';
    const HEADERS = 'header';
    const BODY = 'body';

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('exec');
        $this->setDescription('Run specified action');
        $this->setHelp('Not implemented');
        $this->addArgument(self::ACTION, InputArgument::REQUIRED, 'The action to be executed');

        $this->addOption(
            self::HEADERS,
            '-H',
            (InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY),
            'The list of headers',
            []
        );

        $this->addOption(
            self::BODY,
            '-B',
            InputOption::VALUE_OPTIONAL,
            'The payload of the action',
            '{}'
        );
    }

    /**
     * Defines the behaviour of the command.
     *
     * @param InputInterface  $input  Defines inputs
     * @param OutputInterface $output Defines outputs
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $headers = (object)[];
        foreach ($input->getOption(self::HEADERS) as $header) {
            $pattern = '/^(\w*)=(.*)$/';
            if (preg_match($pattern, $header, $matches) !== false) {
                [$full, $key, $value] = $matches;
                $field = strtolower($key);
                $headers->$field = $value;
            }
        }

        $headers->action = $input->getArgument(self::ACTION);
        $actions = $this->getActions();
        $action = $actions->getAction($input->getArgument(self::ACTION));
        $body = \GuzzleHttp\json_decode($input->getOption(self::BODY));
        $action->handle($headers, $body);
    }
}
