<?php

namespace Database\Seeders;

use App\Models\Owner;
use App\Models\Status;
use Illuminate\Database\Seeder;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ownerValid = Status::where('comment->code', 'OWNER_VALID')->first();

        $datas = [
            [
                'siren' => fake()->siren(),
                'kbis' => 'data:application/pdf;base64,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Ci9Db3VudCAxCi9LaWRzIFs5IDAgUiBdID4+CmVuZG9iagoyIDAgb2JqCjw8CiAgL1R5cGUgL0NhdGFsb2cKICAvUGFnZXMgMSAwIFIKICAvTGFuZyAo0TklUX7bSboyfX1YvU1qPnULjk0pCiAgL01ldGFkYXRhIDggMCBSCiAgL1BhZ2VMYWJlbHMgMTAgMCBSCj4+CgplbmRvYmoKMyAwIG9iago8PAogIC9Gb250IDw8CiAgL0Y1IDI2IDAgUgogIC9GOCAyNyAwIFIKICAvRjYgMjggMCBSCiAgL0Y3IDI5IDAgUgo+PgoKICAvUHJvY1NldCBbL1BERiAvSW1hZ2VCIC9JbWFnZUMgL1RleHRdCiAgL1hPYmplY3QgPDwKICAvSW0xIDExIDAgUgogIC9JbTIgMTUgMCBSCiAgL0ltMyAxNyAwIFIKPj4KCiAgL0NvbG9yU3BhY2UgPDwgL0RlZmF1bHRSR0IgNyAwIFIgPj4KCj4+CgplbmRvYmoKNSAwIG9iago8PCAvRmlsdGVyIC9TdGFuZGFyZAovViAxCi9SIDIKL0xlbmd0aCA0MAovUCAtNjAKL08gPEI0QjYwQTc5NUEwQzhFNDZDRTc5MDRBNTU1M0NFNkQ3NkY3Q0VDMTRBRUQxOUQ5QjM3MjlCQzFBRjhDNzg5NEI+Ci9VIDw5ODY4N0JGN0JFQkNCMTAxRDMxNzZCNzc1NUQ2NjVGMzJDNDI4M0UyNjdCNzAyQjI3Nzc2QzMyRDNFMDc1RDgzPgo+PgplbmRvYmoKMTAgMCBvYmoKPDwgL051bXMgWzAgPDwgL1AgKB8VGqMpID4+Cl0gPj4KCmVuZG9iagp4cmVmCjAgMzAKMDAwMDAwMDAwMCA2NTUzNSBmIAowMDAwMDI0Mjc3IDAwMDAwIG4gCjAwMDAwMjQzMzUgMDAwMDAgbiAKMDAwMDAyNDQ1OSAwMDAwMCBuIAowMDAwMDAwMDE1IDAwMDAwIG4gCjAwMDAwMjQ2ODcgMDAwMDAgbiAKMDAwMDAwMDIyNiAwMDAwMCBuIAowMDAwMDAyOTA4IDAwMDAwIG4gCjAwMDAwMDI5NDEgMDAwMDAgbiAKMDAwMDAyMzU1MCAwMDAwMCBuIAowMDAwMDI0ODk0IDAwMDAwIG4gCjAwMDAwMDM5MDAgMDAwMDAgbiAKMDAwMDAwNDU2NyAwMDAwMCBuIAowMDAwMDA0NTg4IDAwMDAwIG4gCjAwMDAwMDQ2MDggMDAwMDAgbiAKMDAwMDAwNDYyOCAwMDAwMCBuIAowMDAwMDE5NzU0IDAwMDAwIG4gCjAwMDAwMTk3NzYgMDAwMDAgbiAKMDAwMDAyMDcwOSAwMDAwMCBuIAowMDAwMDIwNzI5IDAwMDAwIG4gCjAwMDAwMjA4MzUgMDAwMDAgbiAKMDAwMDAyMzUxNiAwMDAwMCBuIAowMDAwMDIwOTc1IDAwMDAwIG4gCjAwMDAwMjExMzUgMDAwMDAgbiAKMDAwMDAyMTI3NSAwMDAwMCBuIAowMDAwMDIzODE1IDAwMDAwIG4gCjAwMDAwMjM4MzYgMDAwMDAgbiAKMDAwMDAyMzk0NSAwMDAwMCBuIAowMDAwMDI0MDU5IDAwMDAwIG4gCjAwMDAwMjQxNjkgMDAwMDAgbiAKdHJhaWxlcgo8PAogIC9Sb290IDIgMCBSCiAgL0luZm8gNCAwIFIKICAvSUQgWzw1ODk5NUU4NTUwMkE1M0FCNTMyMzdDNTdDRjlFQzBCOD4gPDU4OTk1RTg1NTAyQTUzQUI1MzIzN0M1N0NGOUVDMEI4Pl0KICAvRW5jcnlwdCA1IDAgUgogIC9TaXplIDMwCj4+CnN0YXJ0eHJlZgoyNDk0NAolJUVPRgo=',
                'status_id' => $ownerValid->status_id,
                'company_name' => fake()->company(),
                'phone' => '0606060606',
            ],
        ];
        Owner::create($datas[0]);
    }
}
