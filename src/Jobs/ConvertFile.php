<?php

namespace Eboost\Unoconv\Jobs;

use Eboost\Unoconv\ConvertFile as InOutFile;
use Eboost\Unoconv\Transport\AbstractTransport;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ConvertFile implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, DispatchesJobs;

    /**
     * @var AbstractTransport
     */
    protected $transport;

    /**
     * @var InOutFile
     */
    private $input;

    /**
     * @var InOutFile
     */
    private $output;

    /**
     * @var null
     */
    private $afterJob;

    /**
     * Create a new job instance.
     *
     * @param AbstractTransport $transport
     * @param InOutFile $input
     * @param InOutFile $output
     * @param null $afterJob
     */
    public function __construct(AbstractTransport $transport, InOutFile $input, InOutFile $output, $afterJob = null)
    {
        $this->transport = $transport;
        $this->input = $input;
        $this->output = $output;
        $this->afterJob = $afterJob;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->transport->convert($this->input, $this->output);

        if ($this->afterJob) {
            $this->dispatchNow($this->afterJob);
        }
    }
}
