<?php

/*
 * This file is part of the Novo SGA project.
 *
 * (c) Rogerio Lino <rogeriolino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Novosga\Entity\Unidade;
use Novosga\Service\AtendimentoService;
use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * ResetCommand.
 *
 * @author Rogerio Lino <rogeriolino@gmail.com>
 */
class ResetCommand extends Command
{
    protected static $defaultName = 'novosga:reset';

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var AtendimentoService
     */
    private $atendimentoService;

    public function __construct(ObjectManager $om, AtendimentoService $atendimentoService)
    {
        parent::__construct();
        $this->om                 = $om;
        $this->atendimentoService = $atendimentoService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Reinicia a numeração das senhas de todas ou uma única unidade.')
            ->addArgument(
                'unidade',
                InputArgument::OPTIONAL,
                'Id da unidade a ser reiniciada'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = (int) $input->getArgument('unidade');
        
        if ($id > 0) {
            // verificando unidade
            $unidade = $this->om->find(Unidade::class, $id);
            if (!$unidade) {
                throw new Exception("Unidade inválida: $id");
            }
        }
        
        $this->atendimentoService->acumularAtendimentos($id);
        
        $io = new SymfonyStyle($input, $output);
        $io->success('Senhas reiniciadas com sucesso');
    }
}
