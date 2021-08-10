<?php

namespace App\Command;

use App\Services\CommandOutputHandler;
use App\Services\InstanceClient;
use App\Services\InstanceRepository;
use DigitalOceanV2\Exception\ExceptionInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: InstanceIsHealthyCommand::NAME,
    description: 'Perform instance health check',
)]
class InstanceIsHealthyCommand extends Command
{
    public const NAME = 'app:instance:is-healthy';
    public const EXIT_CODE_ID_INVALID = 3;
    public const EXIT_CODE_NOT_FOUND = 4;

    private const OPTION_ID = 'id';

    public function __construct(
        private InstanceRepository $instanceRepository,
        private InstanceClient $instanceClient,
        private CommandOutputHandler $outputHandler,
    ) {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->addOption(self::OPTION_ID, null, InputOption::VALUE_REQUIRED, 'Instance ID')
        ;
    }

    /**
     * @throws ExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->outputHandler->setOutput($output);

        $id = $this->getIdFromInput($input);

        if (null === $id) {
            $presentationId = $input->getOption(self::OPTION_ID);
            if (is_array($presentationId)) {
                $presentationId = 'array: ' . implode(',', $presentationId);
            }

            $this->outputHandler->createErrorOutput('id-invalid', ['id' => $presentationId]);

            return self::EXIT_CODE_ID_INVALID;
        }

        $instance = $this->instanceRepository->find($id);
        if (null === $instance) {
            $this->outputHandler->createErrorOutput('not-found', ['id' => $id]);

            return self::EXIT_CODE_NOT_FOUND;
        }

        $health = $this->instanceClient->getHealth($instance);

        $this->outputHandler->createSuccessOutput([
            'is-healthy' => $health->isAvailable(),
            'services' => $health->jsonSerialize(),
        ]);

        return $health->isAvailable() ? Command::SUCCESS : Command::FAILURE;
    }

    private function getIdFromInput(InputInterface $input): ?int
    {
        $id = $input->getOption(self::OPTION_ID);

        return ctype_digit($id) ? (int) $id : null;
    }
}
