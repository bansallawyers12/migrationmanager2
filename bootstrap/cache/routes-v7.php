<?php

app('router')->setCompiledRoutes(
    array (
  'compiled' => 
  array (
    0 => false,
    1 => 
    array (
      '/oauth/token' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.token',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/oauth/authorize' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.authorizations.authorize',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'passport.authorizations.approve',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            '_route' => 'passport.authorizations.deny',
          ),
          1 => NULL,
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/oauth/token/refresh' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.token.refresh',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/oauth/tokens' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.tokens.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/oauth/clients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.clients.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'passport.clients.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/oauth/scopes' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.scopes.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/oauth/personal-access-tokens' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.personal.tokens.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'passport.personal.tokens.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/sanctum/csrf-cookie' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'sanctum.csrf-cookie',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/_ignition/health-check' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ignition.healthCheck',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/_ignition/execute-solution' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ignition.executeSolution',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/_ignition/update-config' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ignition.updateConfig',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ZIBmn9Zx19KEEDTd',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/admin-login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::hIlDVZp9kePv06EB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/refresh' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ibIMDcGrFdEUmA15',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/forgot-password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::voMsznFhTVXI6M4X',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/reset-password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::p51u40jI0Q6kH2cw',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/logout' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::b7mnZHEzdxAlBTPl',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/logout-all' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::WzTSvJmf3sqgnDAp',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/profile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::cHkLTRYsqvmav86M',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::i4aaQpRE43vRdqYg',
          ),
          1 => NULL,
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/update-password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::XnyCxOvHm71fJIay',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::vPrLwpiq8n00EsG9',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/recent-cases' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::xKEZ4UCkbalv1pZm',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/documents' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::UGT86qpvmiuBn5VU',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/upcoming-deadlines' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::bLUf8ZSdgyhPGD5Y',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/recent-activity' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::tfSYvIGPgstreJgR',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/matters' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::IEsvOOw8eTaBrocO',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/documents/personal/categories' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::D35DeMfCsgiJYxN6',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/documents/personal/checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::2ujxuEc6Hfc8D07h',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/documents/visa/categories' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5ZG03CRXFciq4UBV',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/documents/visa/checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8zujZDoUWfe7XuL0',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/documents/checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::LdewMadXnuNteH9i',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/documents/upload' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::oAU1bQf5CyTkg0Go',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/workflow/stages' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Exh3SSQXiErrBeyI',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/workflow/allowed-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::DJDJ1t5nuO69l8uT',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/workflow/upload-allowed-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::bzDUZi8pwTXKtjx8',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/messages/send' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::JjuBCpgkxybrgjQ3',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/messages' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::vhSXLohpp0suIbj1',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/messages/unread-count' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ykYVx8Wv1XwC2ms5',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/broadcasting/auth' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Z6UYnwrq9YZWvudG',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/service-account/generate-token' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::bcrcCsY7RfcCO9ZR',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YEZc4jNR5Wmd3Uns',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clear-cache' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::WHm4WYRycA1kWiD5',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/exception' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'exception.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'exception.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/matter' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matter.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/matter/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matter.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/matter/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matter.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/tags' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.tags.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/tags/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.tags.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/tags/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.tags.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/workflow' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.workflow.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/workflow/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.workflow.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/workflow/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.workflow.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/emails' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.emails.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/emails/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.emails.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/emails/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.emails.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/crm-email-template' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.crmemailtemplate.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/crm-email-template/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.crmemailtemplate.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/crm-email-template/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.crmemailtemplate.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/matter-email-template' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matteremailtemplate.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/matter-email-template/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matteremailtemplate.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/matter-email-template/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matteremailtemplate.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/matter-other-email-template' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matterotheremailtemplate.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/matter-other-email-template/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matterotheremailtemplate.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/matter-other-email-template/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matterotheremailtemplate.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/personal-document-type' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.personaldocumenttype.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/personal-document-type/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.personaldocumenttype.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/personal-document-type/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.personaldocumenttype.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/visa-document-type' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.visadocumenttype.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/visa-document-type/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.visadocumenttype.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/visa-document-type/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.visadocumenttype.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/document-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.documentchecklist.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/document-checklist/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.documentchecklist.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/document-checklist/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.documentchecklist.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/sms/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/sms/history' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.history',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/sms/statistics' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.statistics',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/sms/send' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.send.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.send',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/sms/send/template' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.send.template',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/sms/send/bulk' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.send.bulk',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/sms/templates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.templates.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.templates.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/sms/templates/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.templates.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/sms/templates-active' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.templates.active',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/esignature' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.esignature.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/esignature/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.esignature.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/users' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/users/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/users/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/roles' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.roles.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/roles/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.roles.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/roles/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.roles.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/teams' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.teams.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/teams/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.teams.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/offices' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.offices.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/offices/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.offices.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/offices/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.offices.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/clientsemaillist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.clientsemaillist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/database/anzsco' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.database.anzsco.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/database/anzsco/data' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.database.anzsco.data',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/database/anzsco/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.database.anzsco.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/database/anzsco/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.database.anzsco.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.login',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::r1yXbM7ENW4ZcWmo',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.login.post',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/logout' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.logout',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/dashboard/column-preferences' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.dashboard.column-preferences',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/dashboard/update-stage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.dashboard.update-stage',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/dashboard/extend-deadline' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.dashboard.extend-deadline',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/dashboard/update-task-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.dashboard.update-task-completed',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/dashboard/fetch-notifications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.dashboard.fetch-notifications',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/dashboard/fetch-office-visit-notifications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.dashboard.fetch-office-visit-notifications',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/dashboard/mark-notification-seen' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.dashboard.mark-notification-seen',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/dashboard/fetch-visa-expiry-messages' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.dashboard.fetch-visa-expiry-messages',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/dashboard/fetch-in-person-waiting-count' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.dashboard.fetch-in-person-waiting-count',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/dashboard/fetch-total-activity-count' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.dashboard.fetch-total-activity-count',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/dashboard/check-checkin-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.dashboard.check-checkin-status',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/dashboard/update-checkin-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.dashboard.update-checkin-status',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/my_profile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.my_profile',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.my_profile.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/change_password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.change_password',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.change_password.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::68hT7Ec8yqnRGRt2',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/approved_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::zQLWbZqt0r1evLna',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/process_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::bmNN2TdUvq9ceGt6',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/archive_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::IZx0DwJ5VWg0YU8B',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/declined_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::WhBCGtmlzevM08gO',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/delete_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Blk9dGsPpjg11B1U',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/move_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::S7YLwgSCQtMJM1LH',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/appointments-education' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments-education',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/appointments-jrp' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments-jrp',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/appointments-tourist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments-tourist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/appointments-others' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments-others',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/add_ckeditior_image' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'add_ckeditior_image',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get_chapters' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.get_chapters',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get_states' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::617wbGMCQ44oLOVZ',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/settings/taxes/returnsetting' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.returnsetting',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/settings/taxes/savereturnsetting' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.savereturnsetting',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getsubcategories' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9ewCykq3FmugZlGi',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getassigneeajax' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::rHcjggzKLFTyiToP',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getpartnerajax' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::oyHgj4NIBhUxE6mH',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/checkclientexist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::BGTh1ZUmOBuHfVuG',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/leads' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/leads/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/leads/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/leads/followups/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.followups.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/leads/analytics/trends' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.analytics.trends',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/leads/analytics/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.analytics.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/leads/analytics/compare-agents' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.analytics.compare',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-notedetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.get-notedetail',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/email_templates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.email.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/email_templates/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.email.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/email_templates/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.email.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/edit_email_template' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.edit_email_template.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/api-key' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.edit_api',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.edit_api.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clientsmatterslist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.clientsmatterslist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clientsemaillist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.clientsemaillist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/save-section' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.saveSection',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/phone/send-otp' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.phone.sendOTP',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/phone/verify-otp' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.phone.verifyOTP',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/phone/resend-otp' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.phone.resendOTP',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/email/send-verification' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.email.sendVerification',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/email/resend-verification' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.email.resendVerification',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/followup/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::gZwvuREmMnAY6rpE',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/followup/retagfollowup' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::lWdSAXze07iPDAuM',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/removetag' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::yvsiY7FzzKxFKfp2',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/get-recipients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getrecipients',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/get-onlyclientrecipients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getonlyclientrecipients',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/get-allclients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getallclients',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/change_assignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::nrSzRbSCPfO4gxF4',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-templates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.gettemplates',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/sendmail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.sendmail',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/upload-mail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::3aNeCto0FLEyAP1j',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/upload-fetch-mail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::z5zWkNmPtElFi4we',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/upload-sent-fetch-mail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ItbeUIEkw989TuGL',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/reassiginboxemail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.reassiginboxemail',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/reassigsentemail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.reassigsentemail',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/listAllMattersWRTSelClient' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.listAllMattersWRTSelClient',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/updatemailreadbit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.updatemailreadbit',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/filter-emails' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.filter.emails',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/filter-sentemails' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.filter.sentmails',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/mail/enhance' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.mail.enhance',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/create-note' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.createnote',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update-note-datetime' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.updateNoteDatetime',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getnotedetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getnotedetail',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/deletenote' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.deletenote',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/viewnotedetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Ela76R7BgDcq0Vzc',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/viewapplicationnote' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Xszy501Z7bSTTTXN',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/saveprevvisa' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::UPdNLXoJIDUO6Vw6',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/saveonlineprimaryform' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::bEtJvqUf54njC1nP',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/saveonlinesecform' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Fz6U3eweh40IR3mY',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/saveonlinechildform' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::EiM4wGQ98gyivfG6',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-notes' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getnotes',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/pinnote' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YpCYQlsXo5fMS5p0',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/convert-activity-to-note' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.convertActivityToNote',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/archived' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.archived',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/change-client-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.updateclientstatus',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-activities' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.activities',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/deletecostagreement' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.deletecostagreement',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/deleteactivitylog' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.deleteactivitylog',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/not-picked-call' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.notpickedcall',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/pinactivitylog' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::DEwLyVJzWqf2Aojl',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/interested-service' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::lxS99ZcseKT5qcUb',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/edit-interested-service' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::LyFFCKtmPM0L7hs7',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-services' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::CfLn1fmpu0dGlUfK',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/servicesavefee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8thN4iANijZ0zEUA',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getintrestedservice' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::UeOpnQXRtYgIi5jo',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getintrestedserviceedit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::16krqIJdIVsstXAA',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-application-lists' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getapplicationlists',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/saveapplication' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.saveapplication',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/convertapplication' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.convertapplication',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/deleteservices' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.deleteservices',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/savetoapplication' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::qH3htWKSq19Eeg4x',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/client/createservicetaken' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::4j9lCuXbWH966d9l',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/client/removeservicetaken' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::RadZGeHEoRGpk34e',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/client/getservicetaken' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::yK0MoWqkW7PLQNhT',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documents/add-edu-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.documents.addedudocchecklist',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documents/upload-edu-document' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.documents.uploadedudocument',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documents/add-visa-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.documents.addvisadocchecklist',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documents/upload-visa-document' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.documents.uploadvisadocument',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documents/rename' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.documents.renamedoc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documents/delete' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.documents.deletedocs',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documents/get-visa-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.documents.getvisachecklist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documents/not-used' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.documents.notuseddoc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documents/rename-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.documents.renamechecklistdoc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documents/back-to-doc' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.documents.backtodoc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documents/download' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.documents.download',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documents/add-personal-category' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.documents.addPersonalDocCategory',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documents/update-personal-category' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.documents.updatePersonalDocCategory',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documents/add-visa-category' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.documents.addVisaDocCategory',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documents/update-visa-category' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.documents.updateVisaDocCategory',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/saveaccountreport' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.saveaccountreport.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/saveinvoicereport' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.saveinvoicereport.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/saveadjustinvoicereport' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.saveadjustinvoicereport.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/saveofficereport' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.saveofficereport.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/savejournalreport' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.savejournalreport.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/isAnyInvoiceNoExistInDB' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.isAnyInvoiceNoExistInDB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/listOfInvoice' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.listOfInvoice',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/getTopReceiptValInDB' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getTopReceiptValInDB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/getInfoByReceiptId' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getInfoByReceiptId',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/getTopInvoiceNoFromDB' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getTopInvoiceNoFromDB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/clientLedgerBalanceAmount' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.clientLedgerBalanceAmount',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/invoicelist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.invoicelist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/void_invoice' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'client.void_invoice',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/clientreceiptlist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.clientreceiptlist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/officereceiptlist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.officereceiptlist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/journalreceiptlist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.journalreceiptlist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/validate_receipt' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'client.validate_receipt',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/delete_receipt' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::TRqmdzPVErBTzMWy',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update-client-funds-ledger' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.update-client-funds-ledger',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/invoiceamount' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.invoiceamount',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/update-address' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.updateAddress',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/search-address-full' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.searchAddressFull',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/get-place-details' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getPlaceDetails',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/address_auto_populate' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::33hZgO9qlrUJsxGo',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/fetchClientContactNo' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::bUgdSuTLthcL90aE',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/clientdetailsinfo' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.clientdetailsinfo.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/admin/get-visa-types' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.getVisaTypes',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/admin/get-countries' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.getCountries',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/updateOccupation' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.updateOccupation',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/admin/clients/search-partner' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.searchPartner',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/admin/clients/search-partner-test' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.searchPartnerTest',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/admin/clients/test-bidirectional' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.testBidirectional',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/admin/clients/save-relationship' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.saveRelationship',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/generateagreement' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.generateagreement',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/getMigrationAgentDetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getMigrationAgentDetail',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/getVisaAggreementMigrationAgentDetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getVisaAggreementMigrationAgentDetail',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/getCostAssignmentMigrationAgentDetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getCostAssignmentMigrationAgentDetail',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/savecostassignment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.savecostassignment',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/check-cost-assignment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Q7ulBe59JrVzw8ma',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/savecostassignmentlead' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.savecostassignmentlead',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/getCostAssignmentMigrationAgentDetailLead' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getCostAssignmentMigrationAgentDetailLead',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/admin/forms' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'forms.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-matter-templates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getmattertemplates',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/fetchClientMatterAssignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::bwj9uMRB8Wbxssw4',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/updateClientMatterAssignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::38ZhhM5z2UlO2Y8M',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/upload-checklists' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.upload_checklists.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/upload-checklists/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.upload_checklistsupload',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/personalfollowup/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YVTl0Jwr79DH6r8d',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/updatefollowup/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::HtU4DyMAAyihO7Jo',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/reassignfollowup/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::qiSJOpAIhNcaU8F3',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/update-session-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.updatesessioncompleted',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/getAllUser' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getAllUser',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/toggle-client-portal' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.toggleClientPortal',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/anzsco/search' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.anzsco.search',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/check-email' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'check.email',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/check.phone' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'check.phone',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/save_tag' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Y4kUFOykwX1F5aJ0',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/save-references' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'references.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/check-star-client' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'check.star.client',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/merge_records' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'client.merge_records',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/send-webhook' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.send-webhook',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/fetch-visa_expiry_messages' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::IEKH9RRyaKAobQq8',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/applications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.applications.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/applications/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.applications.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/applications-import' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.applications.import',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getapplicationdetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::GepiPhwIQTFn3yyN',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/load-application-insert-update-data' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::naRYbelwPP9xCBVs',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/updatestage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::0R3l1mTg0MxOXU1v',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/completestage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::urFyOagwFjnautbq',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/updatebackstage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::x8CTRivEpLqkGcxC',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-applications-logs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Dx7tHSBLsdQPHNWd',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-applications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::VSpYuVRC4B32T0oS',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/discontinue_application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::vY5i8K0mm82XsFol',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/revert_application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::CCuUi0gRd75hcddx',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/create-app-note' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ynDZX17gYhTu3Lq9',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getapplicationnotes' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::itxktcm3RwYOEu90',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application-sendmail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::rGXXGZobx8AQ4exp',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/updateintake' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::zGS5bXZVLRjMGSED',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/updatedates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::TrfptvDdOuQv4mdu',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/updateexpectwin' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::zRt7SdjlCVJqtWds',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/getapplicationbycid' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::SH5R6w9d7jrmnK7M',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/spagent_application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YCNIbj0JmK6nbsnh',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/sbagent_application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::EPDP2V906oe4pqQb',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/application_ownership' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::o0t1MTXeIh54SyOR',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/superagent' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::pRh6lYJvaiC07Zor',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/subagent' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::JuceiNXdAdGa9YR8',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/applicationsavefee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5898wMY7UWaMFu3Y',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/add-checklists' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ygr9QlPxesxxHP5k',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/checklistupload' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::yx91aaDpHMygF9vS',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/deleteapplicationdocs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::klDXooLDw9icZXn5',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/publishdoc' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::T7Cz3r2wTzIMjzIy',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/migration' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.migration.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/office-visits' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.officevisits.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/office-visits/waiting' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.officevisits.waiting',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/office-visits/attending' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.officevisits.attending',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/office-visits/completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.officevisits.completed',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/office-visits/archived' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.officevisits.archived',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/office-visits/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.officevisits.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/checkin' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::UKCPTBgEded79VTB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-checkin-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::2mlTUz2xQ6jX43we',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update_visit_purpose' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::l8RZBOo6m4QZNAXX',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update_visit_comment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::jjxT3fFOpKLqqOvn',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/attend_session' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::E0wleZrK7BNhFDOa',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/complete_session' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::DB54YSDFwAXNQIuz',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/office-visits/change_assignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::APmbvu7Y2ghiR8Qd',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/appointments' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'appointments.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/appointments/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/appointments-adelaide' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments-adelaide',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/deleteappointment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::gFiPSxSsWNyQpXel',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/add-appointment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::fykTFaCQVQcSZ0r3',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/add-appointment-book' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9zodyFlC73QEilFR',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/editappointment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::zVI2T1zOShUBiVOs',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/updatefollowupschedule' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::eTrc6VgkfhXNNXgc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update_appointment_status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9cCjE6Zvagq9pHJu',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update_appointment_priority' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::hCAjCqXki3keit9i',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update_apppointment_comment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::iz4CbA4YBdyVimx4',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update_apppointment_description' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::7i40W2wvCrl9Qm0i',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-appointments' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::wwLHq6LIsevE7KDo',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getAppointmentdetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::rPekgIaAWXMcFokN',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-assigne-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::sDgQ3k051x9tY1oB',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/change_assignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::MY3UIorbtyz1WyxU',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getdatetimebackend' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'getdatetimebackend',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getdisableddatetime' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'getdisableddatetime',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/booking/appointments' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/booking/appointments/bulk-update-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.bulk-update-status',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/booking/appointments/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/booking/sync/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.sync.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/booking/sync/stats' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.sync.stats',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/booking/sync/manual' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.sync.manual',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/booking/api/appointments' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.api.appointments',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/audit-logs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.auditlogs.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/fetch-notification' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::m6ncriDYZ3Ld3kBA',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/fetch-messages' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::jDQmS5E0C3gx0gNu',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/fetch-office-visit-notifications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::dwAfA5fyEeCWXFOV',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/mark-notification-seen' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::GyfdphATR4KzfAGW',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/check-checkin-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Po9QjsiMaJPeq5nq',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update-checkin-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::eVOhN1VRF2ZmuHz3',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/all-notifications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::j0n6YDeo4gu05zph',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/fetch-InPersonWaitingCount' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::2EKyJHvILvOWqga9',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/fetch-TotalActivityCount' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::GL3FHmiwWMmCprYa',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/assignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/assignee/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/assignee-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::HLdGWR5UhufK3Daj',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update-task-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::yU8gqx30wQOPVXE7',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update-task-not-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Jh5OTFEbsuZHJZG2',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/assigned_by_me' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.assigned_by_me',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/assigned_to_me' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.assigned_to_me',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/action_completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.action_completed',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/is_email_unique' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5hISzjh0jqCC1Hdh',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/is_contactno_unique' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8nNg6NhO8Nchlok1',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/extenddeadlinedate' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::MxcFexmuxXvzqjtk',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update-stage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::L2gjNtfgoi0S1avq',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get_assignee_list' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::F9W8EG9aDnr8p3mh',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update-task' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::dW0DZ0NARDGFCnmk',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/action/counts' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'action.counts',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.action',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/action/list' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'action.list',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documents/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.documents.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/test-signature' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'test.signature',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/doc-to-pdf' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.doc-to-pdf.form',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/doc-to-pdf/convert' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.doc-to-pdf.convert',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/doc-to-pdf/test' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.doc-to-pdf.test',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/doc-to-pdf/test-python' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.doc-to-pdf.test-python',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/doc-to-pdf/debug' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.doc-to-pdf.debug',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/signatures' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.signatures.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.signatures.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/signatures/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.signatures.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/signatures/suggest-association' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.signatures.suggest-association',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/signatures/preview-email' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.signatures.preview-email',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/signatures/bulk-archive' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.signatures.bulk-archive',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/signatures/bulk-void' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.signatures.bulk-void',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/signatures/bulk-resend' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.signatures.bulk-resend',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/webhooks/sms/twilio/status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'webhooks.sms.twilio.status',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/webhooks/sms/twilio/incoming' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'webhooks.sms.twilio.incoming',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/webhooks/sms/cellcast/status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'webhooks.sms.cellcast.status',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/webhooks/sms/cellcast/incoming' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'webhooks.sms.cellcast.incoming',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/test-messaging' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::xbU8ygw7cGcpWcJm',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/test-broadcast' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::oz2er1gS4mYyQ4s9',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/test-create-message' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::IafOPAGMwjKZTjA3',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/broadcasting/auth' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::3klaDjDE75DENAwM',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'POST' => 1,
            'HEAD' => 2,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
    ),
    2 => 
    array (
      0 => '{^(?|/oauth/(?|tokens/([^/]++)(*:32)|clients/([^/]++)(?|(*:58))|personal\\-access\\-tokens/([^/]++)(*:99))|/captcha(?|/api(?:/([^/]++))?(*:136)|(?:/([^/]++))?(*:158))|/a(?|pi/(?|workflow/stages/([^/]++)(*:202)|messages/([^/]++)(?|/read(*:235)|(*:243)))|dmin(?|console/(?|features/(?|matter(?|/(?|edit/([^/]++)(*:309)|([^/]++)(*:325))|\\-(?|email\\-template/(?|edit/([^/]++)(*:371)|([^/]++)(*:387))|other\\-email\\-template/(?|edit/([^/]++)(*:435)|([^/]++)(*:451))))|tags/(?|edit/([^/]++)(*:483)|([^/]++)(*:499))|workflow/(?|edit/([^/]++)(*:533)|([^/]++)(*:549)|deactivate\\-workflow/([^/]++)(*:586)|activate\\-workflow/([^/]++)(*:621))|emails/(?|edit/([^/]++)(*:653)|([^/]++)(*:669))|crm\\-email\\-template/(?|edit/([^/]++)(*:715)|([^/]++)(*:731))|personal\\-document\\-type/(?|edit/([^/]++)(*:781)|([^/]++)(*:797)|checkcreatefolder(*:822))|visa\\-document\\-type/(?|edit/([^/]++)(*:868)|([^/]++)(*:884)|checkcreatefolder(*:909))|document\\-checklist/(?|edit/([^/]++)(*:954)|([^/]++)(*:970))|sms/(?|history/([^/]++)(*:1002)|status/([^/]++)(*:1026)|templates/([^/]++)(?|(*:1056)|/edit(*:1070)|(*:1079))))|system/(?|users/(?|edit/([^/]++)(*:1123)|view/([^/]++)(*:1145)|([^/]++)(*:1162)|s(?|avezone(*:1182)|toreclient(*:1201))|active(*:1217)|in(?|active(*:1237)|vited(*:1251))|c(?|lient(?|list(*:1277)|/([^/]++)(*:1295))|reateclient(*:1316))|editclient/([^/]++)(*:1345))|roles/(?|edit/([^/]++)(*:1377)|([^/]++)(*:1394))|teams/(?|edit/([^/]++)(*:1426)|([^/]++)(*:1443))|offices/(?|edit/([^/]++)(*:1477)|view/(?|([^/]++)(*:1502)|client/([^/]++)(*:1526))|([^/]++)(*:1544)))|database/anzsco/(?|edit/([^/]++)(*:1587)|([^/]++)(*:1604)|import(?|(*:1622))))|/(?|leads/(?|detail/([^/]++)(*:1662)|history/([^/]++)(*:1687)|([^/]++)(?|/edit(*:1712)|(*:1721))|assign(?|(*:1740)|able\\-users(*:1760))|bulk\\-(?|assign(*:1785)|convert(*:1801))|conver(?|t(?|(*:1824)|\\-single(*:1841))|sion\\-stats(*:1862))|followups(?|(*:1884)|/([^/]++)/(?|c(?|omplete(*:1917)|ancel(*:1931))|reschedule(*:1951)))|([^/]++)/followups(*:1980)|analytics(*:1998)|notes/delete/([^/]++)(*:2028)|pin/([^/]++)(*:2049)|updateOccupation(*:2074))|edit_email_template/([^/]++)(*:2112)|clients/(?|e(?|dit/([^/]++)(*:2148)|mail/status/([^/]++)(*:2177))|p(?|artner\\-eoi\\-data/([^/]++)(*:2217)|hone/status/([^/]++)(*:2246))|changetype/([^/]++)/([^/]++)(*:2284)|detail/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?(*:2336)|([^/]++)/eoi\\-roi(?|(*:2365)|/(?|calculate\\-points(*:2395)|([^/]++)(?|(*:2415)|/reveal\\-password(*:2441)))|(*:2452))|s(?|ave(?|a(?|ccountreport/([^/]++)(*:2497)|djustinvoicereport/([^/]++)(*:2533))|invoicereport/([^/]++)(*:2565)|officereport/([^/]++)(*:2595)|journalreport/([^/]++)(*:2626))|endToHubdoc/([^/]++)(*:2656))|gen(?|Invoice/([^/]++)(*:2688)|ClientFundLedgerInvoice/([^/]++)(*:2729)|officereceiptInvoice/([^/]++)(*:2767))|c(?|heckHubdocStatus/([^/]++)(*:2806)|lientdetailsinfo/([^/]++)(*:2840))|printPreview/([^/]++)(*:2871)|([^/]++)/(?|upload\\-agreement(*:2909)|matters(*:2925)))|d(?|ocument(?|/download/pdf/([^/]++)(*:2972)|s(?|(?:/([0-9]+))?(*:2999)|(*:3008)|/(?|([^/]++)(?|/(?|edit(*:3040)|s(?|ign(?|(*:3059))|end\\-(?|reminder(*:3085)|signing\\-link(*:3107)))|page/([^/]++)(*:3131)|download\\-signed(?|(*:3159)|\\-and\\-thankyou(*:3183)))|(*:3194))|thankyou(?:/([^/]++))?(*:3226))))|estroy_(?|by_me/([^/]++)(*:3262)|to_me/([^/]++)(*:3285)|activity/([^/]++)(*:3311)|complete_activity/([^/]++)(*:3346)))|a(?|dmin/forms/([^/]++)(*:3380)|pp(?|lication(?|s/detail/([^/]++)(*:3422)|/export/pdf/([^/]++)(*:3451))|ointments/([^/]++)(?|(*:3482)|/edit(*:3496)|(*:3505)))|ssignee/([^/]++)(?|(*:3535)|/edit(*:3549)|(*:3558)))|forms/([^/]++)/p(?|review(*:3594)|df(*:3605))|get\\-client\\-matters/([^/]++)(*:3644)|up(?|load\\-checklists/matter/([^/]++)(*:3690)|dateappointmentstatus/([^/]++)/([^/]++)(*:3738))|verify\\-email/([^/]++)(*:3770)|booking/(?|appointments/(?|([0-9]+)(*:3814)|([0-9]+)/json(*:3836)|([0-9]+)/update\\-status(*:3868)|([0-9]+)/update\\-consultant(*:3904)|([0-9]+)/add\\-note(*:3931)|([0-9]+)/update\\-followup(*:3965)|([0-9]+)/send\\-reminder(*:3997))|calendar/(paid|jrp|education|tourist|adelaide)(*:4053))|sign(?|/([^/]++)/([^/]++)(*:4088)|atures/([^/]++)(?|(*:4115)|/(?|reminder(*:4136)|send(*:4149)|copy\\-link(*:4168)|associate(*:4186)|detach(*:4201)))))))|/d(?|ebug\\-pdf\\-page/([^/]++)/([^/]++)(*:4254)|ocuments(?|/([^/]++)/(?|sign(*:4291)|page/([^/]++)(*:4313))|(?:/([^/]++))?(*:4337)|/(?|([^/]++)/download\\-signed(?|(*:4378)|\\-and\\-thankyou(*:4402))|thankyou(?:/([^/]++))?(*:4434)|([^/]++)/send\\-reminder(*:4466))))|/sign/([^/]++)/([^/]++)(*:4501)|/test\\-(?|delete\\-message/([^/]++)(*:4544)|mark\\-read/([^/]++)(*:4572)))/?$}sDu',
    ),
    3 => 
    array (
      32 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.tokens.destroy',
          ),
          1 => 
          array (
            0 => 'token_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      58 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.clients.update',
          ),
          1 => 
          array (
            0 => 'client_id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'passport.clients.destroy',
          ),
          1 => 
          array (
            0 => 'client_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      99 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.personal.tokens.destroy',
          ),
          1 => 
          array (
            0 => 'token_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      136 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::LfFMeYLCNUjujNTu',
            'config' => NULL,
          ),
          1 => 
          array (
            0 => 'config',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      158 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::PglOm6iBTWD0XPVy',
            'config' => NULL,
          ),
          1 => 
          array (
            0 => 'config',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      202 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8Oxr594ERJS9PCVW',
          ),
          1 => 
          array (
            0 => 'stage_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      235 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::DgNAaOWNDf61sOLB',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      243 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::NWsPrxGOJ8XuPEpY',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      309 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matter.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      325 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matter.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      371 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matteremailtemplate.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      387 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matteremailtemplate.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      435 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matterotheremailtemplate.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      451 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matterotheremailtemplate.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      483 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.tags.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      499 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.tags.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      533 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.workflow.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      549 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.workflow.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      586 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.workflow.deactivate',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      621 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.workflow.activate',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      653 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.emails.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      669 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.emails.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      715 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.crmemailtemplate.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      731 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.crmemailtemplate.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      781 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.personaldocumenttype.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      797 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.personaldocumenttype.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      822 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      868 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.visadocumenttype.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      884 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.visadocumenttype.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      909 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.generated::acWPDqoSvVDZIaJ2',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      954 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.documentchecklist.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      970 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.documentchecklist.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1002 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.history.show',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1026 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.status.check',
          ),
          1 => 
          array (
            0 => 'smsLogId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1056 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.templates.show',
          ),
          1 => 
          array (
            0 => 'template',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1070 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.templates.edit',
          ),
          1 => 
          array (
            0 => 'template',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1079 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.templates.update',
          ),
          1 => 
          array (
            0 => 'template',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.templates.destroy',
          ),
          1 => 
          array (
            0 => 'template',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1123 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1145 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.view',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1162 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1182 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1201 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.storeclient',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1217 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.active',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1237 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.inactive',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1251 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.invited',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1277 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.clientlist',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1295 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.updateclient',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1316 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.createclient',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1345 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.editclient',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1377 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.roles.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1394 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.roles.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1426 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.teams.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1443 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.teams.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1477 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.offices.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1502 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.offices.view',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1526 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.offices.viewclient',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1544 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.offices.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1587 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.database.anzsco.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1604 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.database.anzsco.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1622 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.database.anzsco.import',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.database.anzsco.import.store',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1662 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.detail',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1687 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.history',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1712 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1721 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.patch',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PATCH' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1740 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.assign',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1760 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.assignable_users',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1785 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.bulk_assign',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1801 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.bulk_convert',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1824 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.convert',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1841 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.convert_single',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1862 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.conversion_stats',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1884 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.followups.index',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.followups.store',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1917 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.followups.complete',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1931 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.followups.cancel',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1951 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.followups.reschedule',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1980 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.followups.get',
          ),
          1 => 
          array (
            0 => 'leadId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1998 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.analytics.index',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2028 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.notes.delete',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2049 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.pin',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2074 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.updateOccupation',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2112 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.edit_email_template',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2148 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2177 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.email.status',
          ),
          1 => 
          array (
            0 => 'emailId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2217 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.partnerEoiData',
          ),
          1 => 
          array (
            0 => 'partnerId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2246 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.phone.status',
          ),
          1 => 
          array (
            0 => 'contactId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2284 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::jOPrF3TgHGDYQ6gN',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'type',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2336 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.detail',
            'client_unique_matter_ref_no' => NULL,
            'tab' => NULL,
          ),
          1 => 
          array (
            0 => 'client_id',
            1 => 'client_unique_matter_ref_no',
            2 => 'tab',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2365 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.eoi-roi.index',
          ),
          1 => 
          array (
            0 => 'admin',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2395 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.eoi-roi.calculatePoints',
          ),
          1 => 
          array (
            0 => 'admin',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2415 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.eoi-roi.show',
          ),
          1 => 
          array (
            0 => 'admin',
            1 => 'eoiReference',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.eoi-roi.destroy',
          ),
          1 => 
          array (
            0 => 'admin',
            1 => 'eoiReference',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2441 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.eoi-roi.revealPassword',
          ),
          1 => 
          array (
            0 => 'admin',
            1 => 'eoiReference',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2452 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.eoi-roi.upsert',
          ),
          1 => 
          array (
            0 => 'admin',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2497 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.saveaccountreport',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2533 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.saveadjustinvoicereport',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2565 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.saveinvoicereport',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2595 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.saveofficereport',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2626 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.savejournalreport',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2656 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.sendToHubdoc',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2688 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::XLUnE5c8uz7U2jal',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2729 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::VDgTKd0mQCTzY61Z',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2767 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::gHcJGWLv0CrqcPij',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2806 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.checkHubdocStatus',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2840 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.clientdetailsinfo',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2871 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::EYxbHxlrbAoToS9l',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2909 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.uploadAgreement',
          ),
          1 => 
          array (
            0 => 'admin',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2925 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.matters',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2972 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::xOj5Id7lBT50gPyg',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2999 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.documents.index',
            'id' => NULL,
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3008 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.documents.store',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3040 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.documents.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3059 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.documents.submitSignatures',
          ),
          1 => 
          array (
            0 => 'document',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.documents.showSignForm',
          ),
          1 => 
          array (
            0 => 'document',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3085 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.documents.sendReminder',
          ),
          1 => 
          array (
            0 => 'document',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3107 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.documents.sendSigningLink',
          ),
          1 => 
          array (
            0 => 'document',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3131 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.documents.page',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'page',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3159 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.documents.download.signed',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3183 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.documents.download_and_thankyou',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3194 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.documents.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PATCH' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3226 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.documents.thankyou',
            'id' => NULL,
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3262 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.destroy_by_me',
          ),
          1 => 
          array (
            0 => 'note_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3285 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.destroy_to_me',
          ),
          1 => 
          array (
            0 => 'note_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3311 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.destroy_activity',
          ),
          1 => 
          array (
            0 => 'note_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3346 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.destroy_complete_activity',
          ),
          1 => 
          array (
            0 => 'note_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3380 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'forms.show',
          ),
          1 => 
          array (
            0 => 'form',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3422 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.applications.detail',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3451 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::TLGWh2efhNCdoGbO',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3482 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments.show',
          ),
          1 => 
          array (
            0 => 'appointment',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3496 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments.edit',
          ),
          1 => 
          array (
            0 => 'appointment',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3505 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments.update',
          ),
          1 => 
          array (
            0 => 'appointment',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'appointments.destroy',
          ),
          1 => 
          array (
            0 => 'appointment',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3535 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.show',
          ),
          1 => 
          array (
            0 => 'assignee',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3549 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.edit',
          ),
          1 => 
          array (
            0 => 'assignee',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3558 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.update',
          ),
          1 => 
          array (
            0 => 'assignee',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.destroy',
          ),
          1 => 
          array (
            0 => 'assignee',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3594 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'forms.preview',
          ),
          1 => 
          array (
            0 => 'form',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3605 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'forms.pdf',
          ),
          1 => 
          array (
            0 => 'form',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3644 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getClientMatters',
          ),
          1 => 
          array (
            0 => 'clientId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3690 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.upload_checklists.matter',
          ),
          1 => 
          array (
            0 => 'matterId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3738 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::roTP3zmhwXmy02Zs',
          ),
          1 => 
          array (
            0 => 'status',
            1 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3770 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.email.verify',
          ),
          1 => 
          array (
            0 => 'token',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3814 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.show',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3836 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.json',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3868 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.update-status',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3904 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.update-consultant',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3931 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.add-note',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3965 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.update-followup',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3997 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.send-reminder',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4053 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.calendar',
          ),
          1 => 
          array (
            0 => 'type',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4088 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.documents.sign',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'token',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4115 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.signatures.show',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4136 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.signatures.reminder',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4149 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.signatures.send',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4168 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.signatures.copy-link',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4186 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.signatures.associate',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4201 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.signatures.detach',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4254 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'debug.pdf.page',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'page',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4291 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'public.documents.submitSignatures',
          ),
          1 => 
          array (
            0 => 'document',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4313 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'public.documents.page',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'page',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4337 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'public.documents.index',
            'id' => NULL,
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4378 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'public.documents.download.signed',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4402 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'public.documents.download_and_thankyou',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4434 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'public.documents.thankyou',
            'id' => NULL,
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4466 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'public.documents.sendReminder',
          ),
          1 => 
          array (
            0 => 'document',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4501 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'public.documents.sign',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'token',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4544 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YpeIb2zQRXslJDvP',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4572 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::95ijkbOwylkrz3di',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => NULL,
          1 => NULL,
          2 => NULL,
          3 => NULL,
          4 => false,
          5 => false,
          6 => 0,
        ),
      ),
    ),
    4 => NULL,
  ),
  'attributes' => 
  array (
    'passport.token' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'oauth/token',
      'action' => 
      array (
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\AccessTokenController@issueToken',
        'as' => 'passport.token',
        'middleware' => 'throttle',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\AccessTokenController@issueToken',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.authorizations.authorize' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'oauth/authorize',
      'action' => 
      array (
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\AuthorizationController@authorize',
        'as' => 'passport.authorizations.authorize',
        'middleware' => 'web',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\AuthorizationController@authorize',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.token.refresh' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'oauth/token/refresh',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\TransientTokenController@refresh',
        'as' => 'passport.token.refresh',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\TransientTokenController@refresh',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.authorizations.approve' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'oauth/authorize',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\ApproveAuthorizationController@approve',
        'as' => 'passport.authorizations.approve',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\ApproveAuthorizationController@approve',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.authorizations.deny' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'oauth/authorize',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\DenyAuthorizationController@deny',
        'as' => 'passport.authorizations.deny',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\DenyAuthorizationController@deny',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.tokens.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'oauth/tokens',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\AuthorizedAccessTokenController@forUser',
        'as' => 'passport.tokens.index',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\AuthorizedAccessTokenController@forUser',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.tokens.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'oauth/tokens/{token_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\AuthorizedAccessTokenController@destroy',
        'as' => 'passport.tokens.destroy',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\AuthorizedAccessTokenController@destroy',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.clients.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'oauth/clients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@forUser',
        'as' => 'passport.clients.index',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@forUser',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.clients.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'oauth/clients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@store',
        'as' => 'passport.clients.store',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@store',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.clients.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'oauth/clients/{client_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@update',
        'as' => 'passport.clients.update',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@update',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.clients.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'oauth/clients/{client_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@destroy',
        'as' => 'passport.clients.destroy',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@destroy',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.scopes.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'oauth/scopes',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\ScopeController@all',
        'as' => 'passport.scopes.index',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\ScopeController@all',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.personal.tokens.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'oauth/personal-access-tokens',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController@forUser',
        'as' => 'passport.personal.tokens.index',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController@forUser',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.personal.tokens.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'oauth/personal-access-tokens',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController@store',
        'as' => 'passport.personal.tokens.store',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController@store',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.personal.tokens.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'oauth/personal-access-tokens/{token_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController@destroy',
        'as' => 'passport.personal.tokens.destroy',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController@destroy',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'sanctum.csrf-cookie' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'sanctum/csrf-cookie',
      'action' => 
      array (
        'uses' => 'Laravel\\Sanctum\\Http\\Controllers\\CsrfCookieController@show',
        'controller' => 'Laravel\\Sanctum\\Http\\Controllers\\CsrfCookieController@show',
        'namespace' => NULL,
        'prefix' => 'sanctum',
        'where' => 
        array (
        ),
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'sanctum.csrf-cookie',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::LfFMeYLCNUjujNTu' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'captcha/api/{config?}',
      'action' => 
      array (
        'uses' => '\\Mews\\Captcha\\CaptchaController@getCaptchaApi',
        'controller' => '\\Mews\\Captcha\\CaptchaController@getCaptchaApi',
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'generated::LfFMeYLCNUjujNTu',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::PglOm6iBTWD0XPVy' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'captcha/{config?}',
      'action' => 
      array (
        'uses' => '\\Mews\\Captcha\\CaptchaController@getCaptcha',
        'controller' => '\\Mews\\Captcha\\CaptchaController@getCaptcha',
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'generated::PglOm6iBTWD0XPVy',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ignition.healthCheck' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => '_ignition/health-check',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'Spatie\\LaravelIgnition\\Http\\Middleware\\RunnableSolutionsEnabled',
        ),
        'uses' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\HealthCheckController@__invoke',
        'controller' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\HealthCheckController',
        'as' => 'ignition.healthCheck',
        'namespace' => NULL,
        'prefix' => '_ignition',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ignition.executeSolution' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => '_ignition/execute-solution',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'Spatie\\LaravelIgnition\\Http\\Middleware\\RunnableSolutionsEnabled',
        ),
        'uses' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\ExecuteSolutionController@__invoke',
        'controller' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\ExecuteSolutionController',
        'as' => 'ignition.executeSolution',
        'namespace' => NULL,
        'prefix' => '_ignition',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ignition.updateConfig' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => '_ignition/update-config',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'Spatie\\LaravelIgnition\\Http\\Middleware\\RunnableSolutionsEnabled',
        ),
        'uses' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\UpdateConfigController@__invoke',
        'controller' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\UpdateConfigController',
        'as' => 'ignition.updateConfig',
        'namespace' => NULL,
        'prefix' => '_ignition',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ZIBmn9Zx19KEEDTd' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@login',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@login',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::ZIBmn9Zx19KEEDTd',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::hIlDVZp9kePv06EB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/admin-login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@adminLogin',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@adminLogin',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::hIlDVZp9kePv06EB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ibIMDcGrFdEUmA15' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/refresh',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@refresh',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@refresh',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::ibIMDcGrFdEUmA15',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::voMsznFhTVXI6M4X' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/forgot-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@forgotPassword',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@forgotPassword',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::voMsznFhTVXI6M4X',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::p51u40jI0Q6kH2cw' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/reset-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@resetPassword',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@resetPassword',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::p51u40jI0Q6kH2cw',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::b7mnZHEzdxAlBTPl' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/logout',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@logout',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@logout',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::b7mnZHEzdxAlBTPl',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::WzTSvJmf3sqgnDAp' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/logout-all',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@logoutAll',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@logoutAll',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::WzTSvJmf3sqgnDAp',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::cHkLTRYsqvmav86M' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@getProfile',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@getProfile',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::cHkLTRYsqvmav86M',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::i4aaQpRE43vRdqYg' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'api/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@updateProfile',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@updateProfile',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::i4aaQpRE43vRdqYg',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::XnyCxOvHm71fJIay' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/update-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@updatePassword',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@updatePassword',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::XnyCxOvHm71fJIay',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::vPrLwpiq8n00EsG9' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@dashboard',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@dashboard',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::vPrLwpiq8n00EsG9',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::xKEZ4UCkbalv1pZm' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/recent-cases',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@recentCaseViewAll',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@recentCaseViewAll',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::xKEZ4UCkbalv1pZm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::UGT86qpvmiuBn5VU' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/documents',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@documentViewAll',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@documentViewAll',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::UGT86qpvmiuBn5VU',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::bLUf8ZSdgyhPGD5Y' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/upcoming-deadlines',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@upcomingDeadlinesViewAll',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@upcomingDeadlinesViewAll',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::bLUf8ZSdgyhPGD5Y',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::tfSYvIGPgstreJgR' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/recent-activity',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@recentActivityViewAll',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@recentActivityViewAll',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::tfSYvIGPgstreJgR',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::IEsvOOw8eTaBrocO' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/matters',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@getAllMatters',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@getAllMatters',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::IEsvOOw8eTaBrocO',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::D35DeMfCsgiJYxN6' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/documents/personal/categories',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@getPersonalDocumentCategories',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@getPersonalDocumentCategories',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::D35DeMfCsgiJYxN6',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::2ujxuEc6Hfc8D07h' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/documents/personal/checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@getPersonalDocumentChecklist',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@getPersonalDocumentChecklist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::2ujxuEc6Hfc8D07h',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5ZG03CRXFciq4UBV' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/documents/visa/categories',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@getVisaDocumentCategories',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@getVisaDocumentCategories',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::5ZG03CRXFciq4UBV',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8zujZDoUWfe7XuL0' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/documents/visa/checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@getVisaDocumentChecklist',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@getVisaDocumentChecklist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::8zujZDoUWfe7XuL0',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::LdewMadXnuNteH9i' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/documents/checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@addDocumentChecklist',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@addDocumentChecklist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::LdewMadXnuNteH9i',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::oAU1bQf5CyTkg0Go' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/documents/upload',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@uploadDocument',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@uploadDocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::oAU1bQf5CyTkg0Go',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Exh3SSQXiErrBeyI' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/workflow/stages',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalWorkflowController@getWorkflowStages',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalWorkflowController@getWorkflowStages',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::Exh3SSQXiErrBeyI',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8Oxr594ERJS9PCVW' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/workflow/stages/{stage_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalWorkflowController@getWorkflowStageDetails',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalWorkflowController@getWorkflowStageDetails',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::8Oxr594ERJS9PCVW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::DJDJ1t5nuO69l8uT' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/workflow/allowed-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalWorkflowController@allowedChecklistForStages',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalWorkflowController@allowedChecklistForStages',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::DJDJ1t5nuO69l8uT',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::bzDUZi8pwTXKtjx8' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/workflow/upload-allowed-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalWorkflowController@uploadAllowedChecklistDocument',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalWorkflowController@uploadAllowedChecklistDocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::bzDUZi8pwTXKtjx8',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::JjuBCpgkxybrgjQ3' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/messages/send',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@sendMessage',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@sendMessage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::JjuBCpgkxybrgjQ3',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::vhSXLohpp0suIbj1' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/messages',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@getMessages',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@getMessages',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::vhSXLohpp0suIbj1',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ykYVx8Wv1XwC2ms5' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/messages/unread-count',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@getUnreadCount',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@getUnreadCount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::ykYVx8Wv1XwC2ms5',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::DgNAaOWNDf61sOLB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/messages/{id}/read',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@markAsRead',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@markAsRead',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::DgNAaOWNDf61sOLB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::NWsPrxGOJ8XuPEpY' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/messages/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@getMessageDetails',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@getMessageDetails',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::NWsPrxGOJ8XuPEpY',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Z6UYnwrq9YZWvudG' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/broadcasting/auth',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:5494:"function (\\Illuminate\\Http\\Request $request) {
    try {
        // Get the authorization header
        $authHeader = $request->header(\'Authorization\');
        
        if (!$authHeader || !\\str_starts_with($authHeader, \'Bearer \')) {
            return \\response()->json([\'error\' => \'Unauthorized\'], 401);
        }

        // Extract token
        $token = \\substr($authHeader, 7);
        
        // Get request data - handle both form and JSON content types
        $socketId = $request->input(\'socket_id\');
        $channelName = $request->input(\'channel_name\');
        
        // Validate token using Sanctum
        $user = \\Laravel\\Sanctum\\PersonalAccessToken::findToken($token)?->tokenable;
        
        if (!$user) {
            \\Log::error(\'Invalid token provided for channel auth\', [\'token\' => \\substr($token, 0, 10) . \'...\']);
            return \\response()->json([\'error\' => \'Invalid token\'], 401);
        }
        
        // Log the request details for debugging
        \\Log::info(\'Broadcasting auth request\', [
            \'content_type\' => $request->header(\'Content-Type\'),
            \'socket_id\' => $socketId,
            \'channel_name\' => $channelName,
            \'user_id\' => $user->id,
            \'request_data\' => $request->all()
        ]);
        
        // Set the authenticated user for the request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        
        // Validate channel name format and authorization
        if (!\\preg_match(\'/^private-(user|matter)\\.\\d+$/\', $channelName)) {
            \\Log::warning(\'Invalid channel format\', [\'user_id\' => $user->id, \'channel\' => $channelName]);
            return \\response()->json([\'error\' => \'Invalid channel format\'], 403);
        }
        
        // Ensure we have required parameters
        if (!$socketId || !$channelName) {
            \\Log::warning(\'Missing required parameters\', [
                \'socket_id\' => $socketId,
                \'channel_name\' => $channelName,
                \'user_id\' => $user->id
            ]);
            return \\response()->json([\'error\' => \'Missing required parameters\'], 400);
        }
        
        // Check channel authorization based on channel type
        if (\\str_starts_with($channelName, \'private-user.\')) {
            $requestedUserId = (int) \\substr($channelName, 13); // Remove \'private-user.\'
            if ($user->id !== $requestedUserId) {
                \\Log::warning(\'User cannot access another user\\\'s channel\', [
                    \'user_id\' => $user->id, 
                    \'requested_user_id\' => $requestedUserId,
                    \'channel\' => $channelName
                ]);
                return \\response()->json([\'error\' => \'Channel access denied\'], 403);
            }
        } elseif (\\str_starts_with($channelName, \'private-matter.\')) {
            $matterId = (int) \\substr($channelName, 15); // Remove \'private-matter.\'
            
            // Check if user is associated with this matter or is superadmin
            $isAssociated = \\Illuminate\\Support\\Facades\\DB::table(\'client_matters\')
                ->where(\'id\', $matterId)
                ->where(function($query) use ($user) {
                    $query->where(\'sel_migration_agent\', $user->id)
                          ->orWhere(\'sel_person_responsible\', $user->id)
                          ->orWhere(\'sel_person_assisting\', $user->id);
                })
                ->exists();
            
            $isSuperAdmin = $user->role == 1;
            
            if (!$isAssociated && !$isSuperAdmin) {
                \\Log::warning(\'User cannot access matter channel\', [
                    \'user_id\' => $user->id, 
                    \'matter_id\' => $matterId,
                    \'channel\' => $channelName
                ]);
                return \\response()->json([\'error\' => \'Channel access denied\'], 403);
            }
        }
        
        \\Log::info(\'Channel auth successful\', [\'user_id\' => $user->id, \'channel\' => $channelName]);

        // Generate auth response using Pusher Cloud
        $pusher = new \\Pusher\\Pusher(
            \\config(\'broadcasting.connections.pusher.key\'),
            \\config(\'broadcasting.connections.pusher.secret\'),
            \\config(\'broadcasting.connections.pusher.app_id\'),
            [
                \'cluster\' => \\config(\'broadcasting.connections.pusher.options.cluster\'),
                \'useTLS\' => \\config(\'broadcasting.connections.pusher.options.useTLS\', true),
                \'encrypted\' => \\config(\'broadcasting.connections.pusher.options.encrypted\', true),
            ]
        );

        $authResponse = $pusher->authorizeChannel($channelName, $socketId);

        return \\response($authResponse, 200, [
            \'Content-Type\' => \'text/plain\'
        ]);
        
    } catch (\\Exception $e) {
        \\Log::error(\'Broadcasting auth error: \' . $e->getMessage(), [
            \'trace\' => $e->getTraceAsString(),
            \'request_data\' => [
                \'socket_id\' => $request->input(\'socket_id\'),
                \'channel_name\' => $request->input(\'channel_name\'),
                \'auth_header\' => $request->header(\'Authorization\') ? \'Present\' : \'Missing\'
            ]
        ]);
        return \\response()->json([\'error\' => \'Authentication failed: \' . $e->getMessage()], 500);
    }
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000af20000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::Z6UYnwrq9YZWvudG',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::bcrcCsY7RfcCO9ZR' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/service-account/generate-token',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ServiceAccountController@generateToken',
        'controller' => 'App\\Http\\Controllers\\API\\ServiceAccountController@generateToken',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::bcrcCsY7RfcCO9ZR',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YEZc4jNR5Wmd3Uns' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => '/',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:62:"function() {
    return \\redirect()->route(\'admin.login\');
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000b100000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::YEZc4jNR5Wmd3Uns',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::WHm4WYRycA1kWiD5' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clear-cache',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:279:"function() {
    \\Artisan::call(\'config:clear\');
    \\Artisan::call(\'view:clear\');
    \\Artisan::call(\'route:clear\');
    \\Artisan::call(\'route:cache\');
    return \\response()->json([
        \'success\' => true,
        \'message\' => \'Cache cleared successfully\'
    ]);
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000b120000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::WHm4WYRycA1kWiD5',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'exception.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'exception',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\ExceptionController@index',
        'controller' => 'App\\Http\\Controllers\\ExceptionController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'exception.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'exception.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'exception',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\ExceptionController@index',
        'controller' => 'App\\Http\\Controllers\\ExceptionController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'exception.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matter.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/matter',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@index',
        'as' => 'adminconsole.features.matter.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matter.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/matter/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@create',
        'as' => 'adminconsole.features.matter.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matter.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/matter/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@store',
        'as' => 'adminconsole.features.matter.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matter.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/matter/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@edit',
        'as' => 'adminconsole.features.matter.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matter.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/matter/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@update',
        'as' => 'adminconsole.features.matter.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.tags.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/tags',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\TagController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\TagController@index',
        'as' => 'adminconsole.features.tags.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.tags.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/tags/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\TagController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\TagController@create',
        'as' => 'adminconsole.features.tags.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.tags.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/tags/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\TagController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\TagController@store',
        'as' => 'adminconsole.features.tags.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.tags.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/tags/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\TagController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\TagController@edit',
        'as' => 'adminconsole.features.tags.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.tags.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/tags/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\TagController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\TagController@update',
        'as' => 'adminconsole.features.tags.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.workflow.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/workflow',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@index',
        'as' => 'adminconsole.features.workflow.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.workflow.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/workflow/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@create',
        'as' => 'adminconsole.features.workflow.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.workflow.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/workflow/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@store',
        'as' => 'adminconsole.features.workflow.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.workflow.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/workflow/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@edit',
        'as' => 'adminconsole.features.workflow.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.workflow.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/workflow/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@update',
        'as' => 'adminconsole.features.workflow.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.workflow.deactivate' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/workflow/deactivate-workflow/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@deactivateWorkflow',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@deactivateWorkflow',
        'as' => 'adminconsole.features.workflow.deactivate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.workflow.activate' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/workflow/activate-workflow/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@activateWorkflow',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@activateWorkflow',
        'as' => 'adminconsole.features.workflow.activate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.emails.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/emails',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@index',
        'as' => 'adminconsole.features.emails.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.emails.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/emails/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@create',
        'as' => 'adminconsole.features.emails.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.emails.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/emails/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@store',
        'as' => 'adminconsole.features.emails.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.emails.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/emails/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@edit',
        'as' => 'adminconsole.features.emails.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.emails.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/emails/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@update',
        'as' => 'adminconsole.features.emails.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.crmemailtemplate.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/crm-email-template',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@index',
        'as' => 'adminconsole.features.crmemailtemplate.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.crmemailtemplate.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/crm-email-template/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@create',
        'as' => 'adminconsole.features.crmemailtemplate.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.crmemailtemplate.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/crm-email-template/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@store',
        'as' => 'adminconsole.features.crmemailtemplate.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.crmemailtemplate.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/crm-email-template/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@edit',
        'as' => 'adminconsole.features.crmemailtemplate.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.crmemailtemplate.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/crm-email-template/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@update',
        'as' => 'adminconsole.features.crmemailtemplate.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matteremailtemplate.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/matter-email-template',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@index',
        'as' => 'adminconsole.features.matteremailtemplate.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matteremailtemplate.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/matter-email-template/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@create',
        'as' => 'adminconsole.features.matteremailtemplate.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matteremailtemplate.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/matter-email-template/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@store',
        'as' => 'adminconsole.features.matteremailtemplate.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matteremailtemplate.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/matter-email-template/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@edit',
        'as' => 'adminconsole.features.matteremailtemplate.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matteremailtemplate.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/matter-email-template/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@update',
        'as' => 'adminconsole.features.matteremailtemplate.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matterotheremailtemplate.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/matter-other-email-template',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@index',
        'as' => 'adminconsole.features.matterotheremailtemplate.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matterotheremailtemplate.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/matter-other-email-template/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@create',
        'as' => 'adminconsole.features.matterotheremailtemplate.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matterotheremailtemplate.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/matter-other-email-template/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@store',
        'as' => 'adminconsole.features.matterotheremailtemplate.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matterotheremailtemplate.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/matter-other-email-template/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@edit',
        'as' => 'adminconsole.features.matterotheremailtemplate.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matterotheremailtemplate.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/matter-other-email-template/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@update',
        'as' => 'adminconsole.features.matterotheremailtemplate.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.personaldocumenttype.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/personal-document-type',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@index',
        'as' => 'adminconsole.features.personaldocumenttype.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.personaldocumenttype.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/personal-document-type/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@create',
        'as' => 'adminconsole.features.personaldocumenttype.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.personaldocumenttype.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/personal-document-type/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@store',
        'as' => 'adminconsole.features.personaldocumenttype.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.personaldocumenttype.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/personal-document-type/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@edit',
        'as' => 'adminconsole.features.personaldocumenttype.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.personaldocumenttype.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/personal-document-type/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@update',
        'as' => 'adminconsole.features.personaldocumenttype.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/personal-document-type/checkcreatefolder',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@checkcreatefolder',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@checkcreatefolder',
        'as' => 'adminconsole.features.',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.visadocumenttype.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/visa-document-type',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@index',
        'as' => 'adminconsole.features.visadocumenttype.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.visadocumenttype.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/visa-document-type/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@create',
        'as' => 'adminconsole.features.visadocumenttype.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.visadocumenttype.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/visa-document-type/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@store',
        'as' => 'adminconsole.features.visadocumenttype.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.visadocumenttype.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/visa-document-type/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@edit',
        'as' => 'adminconsole.features.visadocumenttype.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.visadocumenttype.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/visa-document-type/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@update',
        'as' => 'adminconsole.features.visadocumenttype.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.generated::acWPDqoSvVDZIaJ2' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/visa-document-type/checkcreatefolder',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@checkcreatefolder',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@checkcreatefolder',
        'as' => 'adminconsole.features.generated::acWPDqoSvVDZIaJ2',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.documentchecklist.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/document-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@index',
        'as' => 'adminconsole.features.documentchecklist.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.documentchecklist.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/document-checklist/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@create',
        'as' => 'adminconsole.features.documentchecklist.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.documentchecklist.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/document-checklist/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@store',
        'as' => 'adminconsole.features.documentchecklist.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.documentchecklist.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/document-checklist/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@edit',
        'as' => 'adminconsole.features.documentchecklist.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.documentchecklist.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/document-checklist/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@update',
        'as' => 'adminconsole.features.documentchecklist.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@dashboard',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@dashboard',
        'as' => 'adminconsole.features.sms.dashboard',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.history' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/history',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@history',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@history',
        'as' => 'adminconsole.features.sms.history',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.history.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/history/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@show',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@show',
        'as' => 'adminconsole.features.sms.history.show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.statistics' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/statistics',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@statistics',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@statistics',
        'as' => 'adminconsole.features.sms.statistics',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.status.check' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/status/{smsLogId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@checkStatus',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@checkStatus',
        'as' => 'adminconsole.features.sms.status.check',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.send.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/send',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsSendController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsSendController@create',
        'as' => 'adminconsole.features.sms.send.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.send' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/sms/send',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsSendController@send',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsSendController@send',
        'as' => 'adminconsole.features.sms.send',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.send.template' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/sms/send/template',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsSendController@sendFromTemplate',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsSendController@sendFromTemplate',
        'as' => 'adminconsole.features.sms.send.template',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.send.bulk' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/sms/send/bulk',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsSendController@sendBulk',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsSendController@sendBulk',
        'as' => 'adminconsole.features.sms.send.bulk',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.templates.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/templates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'adminconsole.features.sms.templates.index',
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.templates.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/templates/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'adminconsole.features.sms.templates.create',
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.templates.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/sms/templates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'adminconsole.features.sms.templates.store',
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.templates.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/templates/{template}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'adminconsole.features.sms.templates.show',
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@show',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.templates.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/templates/{template}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'adminconsole.features.sms.templates.edit',
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.templates.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'adminconsole/features/sms/templates/{template}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'adminconsole.features.sms.templates.update',
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.templates.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'adminconsole/features/sms/templates/{template}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'adminconsole.features.sms.templates.destroy',
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@destroy',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@destroy',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.templates.active' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/templates-active',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@active',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@active',
        'as' => 'adminconsole.features.sms.templates.active',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.esignature.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/esignature',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\ESignatureController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\ESignatureController@index',
        'as' => 'adminconsole.features.esignature.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/esignature',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.esignature.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/esignature/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\ESignatureController@exportAudit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\ESignatureController@exportAudit',
        'as' => 'adminconsole.features.esignature.export',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/esignature',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@index',
        'as' => 'adminconsole.system.users.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@create',
        'as' => 'adminconsole.system.users.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/system/users/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@store',
        'as' => 'adminconsole.system.users.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@edit',
        'as' => 'adminconsole.system.users.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.view' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users/view/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@view',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@view',
        'as' => 'adminconsole.system.users.view',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/system/users/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@update',
        'as' => 'adminconsole.system.users.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/system/users/savezone',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@savezone',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@savezone',
        'as' => 'adminconsole.system.',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.active' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users/active',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@active',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@active',
        'as' => 'adminconsole.system.users.active',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.inactive' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users/inactive',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@inactive',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@inactive',
        'as' => 'adminconsole.system.users.inactive',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.invited' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users/invited',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@invited',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@invited',
        'as' => 'adminconsole.system.users.invited',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.clientlist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users/clientlist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@clientlist',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@clientlist',
        'as' => 'adminconsole.system.users.clientlist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.createclient' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users/createclient',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@createclient',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@createclient',
        'as' => 'adminconsole.system.users.createclient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.storeclient' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/system/users/storeclient',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@storeclient',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@storeclient',
        'as' => 'adminconsole.system.users.storeclient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.editclient' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users/editclient/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@editclient',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@editclient',
        'as' => 'adminconsole.system.users.editclient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.updateclient' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/system/users/client/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@updateclient',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@updateclient',
        'as' => 'adminconsole.system.users.updateclient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.roles.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/roles',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@index',
        'as' => 'adminconsole.system.roles.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.roles.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/roles/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@create',
        'as' => 'adminconsole.system.roles.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.roles.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/system/roles/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@store',
        'as' => 'adminconsole.system.roles.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.roles.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/roles/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@edit',
        'as' => 'adminconsole.system.roles.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.roles.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/system/roles/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@update',
        'as' => 'adminconsole.system.roles.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.teams.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/teams',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\TeamController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\TeamController@index',
        'as' => 'adminconsole.system.teams.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.teams.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/teams/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\TeamController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\TeamController@edit',
        'as' => 'adminconsole.system.teams.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.teams.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/system/teams/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\TeamController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\TeamController@store',
        'as' => 'adminconsole.system.teams.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.teams.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/system/teams/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\TeamController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\TeamController@update',
        'as' => 'adminconsole.system.teams.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.offices.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/offices',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@index',
        'as' => 'adminconsole.system.offices.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.offices.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/offices/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@create',
        'as' => 'adminconsole.system.offices.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.offices.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/system/offices/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@store',
        'as' => 'adminconsole.system.offices.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.offices.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/offices/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@edit',
        'as' => 'adminconsole.system.offices.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.offices.view' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/offices/view/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@view',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@view',
        'as' => 'adminconsole.system.offices.view',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.offices.viewclient' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/offices/view/client/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@viewclient',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@viewclient',
        'as' => 'adminconsole.system.offices.viewclient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.offices.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/system/offices/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@update',
        'as' => 'adminconsole.system.offices.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.clientsemaillist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/clientsemaillist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@clientsemaillist',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@clientsemaillist',
        'as' => 'adminconsole.system.clientsemaillist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.database.anzsco.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/database/anzsco',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@index',
        'as' => 'adminconsole.database.anzsco.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/database',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.database.anzsco.data' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/database/anzsco/data',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@getData',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@getData',
        'as' => 'adminconsole.database.anzsco.data',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/database',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.database.anzsco.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/database/anzsco/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@create',
        'as' => 'adminconsole.database.anzsco.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/database',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.database.anzsco.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/database/anzsco/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@store',
        'as' => 'adminconsole.database.anzsco.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/database',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.database.anzsco.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/database/anzsco/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@edit',
        'as' => 'adminconsole.database.anzsco.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/database',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.database.anzsco.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/database/anzsco/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@update',
        'as' => 'adminconsole.database.anzsco.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/database',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.database.anzsco.import' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/database/anzsco/import',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@importPage',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@importPage',
        'as' => 'adminconsole.database.anzsco.import',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/database',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.database.anzsco.import.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/database/anzsco/import',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@import',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@import',
        'as' => 'adminconsole.database.anzsco.import.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/database',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.login' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@showLoginForm',
        'controller' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@showLoginForm',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.login',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::r1yXbM7ENW4ZcWmo' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@showLoginForm',
        'controller' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@showLoginForm',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::r1yXbM7ENW4ZcWmo',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.login.post' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@login',
        'controller' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@login',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.login.post',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.logout' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/logout',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@logout',
        'controller' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@logout',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.logout',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DashboardController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\DashboardController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.dashboard',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.dashboard.column-preferences' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/dashboard/column-preferences',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DashboardController@saveColumnPreferences',
        'controller' => 'App\\Http\\Controllers\\Admin\\DashboardController@saveColumnPreferences',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.dashboard.column-preferences',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.dashboard.update-stage' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/dashboard/update-stage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DashboardController@updateStage',
        'controller' => 'App\\Http\\Controllers\\Admin\\DashboardController@updateStage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.dashboard.update-stage',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.dashboard.extend-deadline' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/dashboard/extend-deadline',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DashboardController@extendDeadlineDate',
        'controller' => 'App\\Http\\Controllers\\Admin\\DashboardController@extendDeadlineDate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.dashboard.extend-deadline',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.dashboard.update-task-completed' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/dashboard/update-task-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DashboardController@updateTaskCompleted',
        'controller' => 'App\\Http\\Controllers\\Admin\\DashboardController@updateTaskCompleted',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.dashboard.update-task-completed',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.dashboard.fetch-notifications' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/dashboard/fetch-notifications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchnotification',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchnotification',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.dashboard.fetch-notifications',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.dashboard.fetch-office-visit-notifications' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/dashboard/fetch-office-visit-notifications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchOfficeVisitNotifications',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchOfficeVisitNotifications',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.dashboard.fetch-office-visit-notifications',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.dashboard.mark-notification-seen' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/dashboard/mark-notification-seen',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@markNotificationSeen',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@markNotificationSeen',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.dashboard.mark-notification-seen',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.dashboard.fetch-visa-expiry-messages' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/dashboard/fetch-visa-expiry-messages',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchvisaexpirymessages',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchvisaexpirymessages',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.dashboard.fetch-visa-expiry-messages',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.dashboard.fetch-in-person-waiting-count' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/dashboard/fetch-in-person-waiting-count',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchInPersonWaitingCount',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchInPersonWaitingCount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.dashboard.fetch-in-person-waiting-count',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.dashboard.fetch-total-activity-count' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/dashboard/fetch-total-activity-count',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchTotalActivityCount',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchTotalActivityCount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.dashboard.fetch-total-activity-count',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.dashboard.check-checkin-status' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/dashboard/check-checkin-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DashboardController@checkCheckinStatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\DashboardController@checkCheckinStatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.dashboard.check-checkin-status',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.dashboard.update-checkin-status' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/dashboard/update-checkin-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DashboardController@updateCheckinStatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\DashboardController@updateCheckinStatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.dashboard.update-checkin-status',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.my_profile' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/my_profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@myProfile',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@myProfile',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.my_profile',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.my_profile.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/my_profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@myProfile',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@myProfile',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.my_profile.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.change_password' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/change_password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@change_password',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@change_password',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.change_password',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.change_password.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/change_password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@change_password',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@change_password',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.change_password.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::68hT7Ec8yqnRGRt2' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@updateAction',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@updateAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::68hT7Ec8yqnRGRt2',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::zQLWbZqt0r1evLna' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/approved_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@approveAction',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@approveAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::zQLWbZqt0r1evLna',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::bmNN2TdUvq9ceGt6' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/process_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@processAction',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@processAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::bmNN2TdUvq9ceGt6',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::IZx0DwJ5VWg0YU8B' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/archive_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@archiveAction',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@archiveAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::IZx0DwJ5VWg0YU8B',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::WhBCGtmlzevM08gO' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/declined_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@declinedAction',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@declinedAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::WhBCGtmlzevM08gO',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Blk9dGsPpjg11B1U' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/delete_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@deleteAction',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@deleteAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Blk9dGsPpjg11B1U',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::S7YLwgSCQtMJM1LH' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/move_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@moveAction',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@moveAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::S7YLwgSCQtMJM1LH',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments-education' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/appointments-education',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@appointmentsEducation',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@appointmentsEducation',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'appointments-education',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments-jrp' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/appointments-jrp',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@appointmentsJrp',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@appointmentsJrp',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'appointments-jrp',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments-tourist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/appointments-tourist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@appointmentsTourist',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@appointmentsTourist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'appointments-tourist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments-others' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/appointments-others',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@appointmentsOthers',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@appointmentsOthers',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'appointments-others',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'add_ckeditior_image' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/add_ckeditior_image',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@addCkeditiorImage',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@addCkeditiorImage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'add_ckeditior_image',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.get_chapters' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/get_chapters',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getChapters',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getChapters',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.get_chapters',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::617wbGMCQ44oLOVZ' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/get_states',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getStates',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getStates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::617wbGMCQ44oLOVZ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.returnsetting' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/settings/taxes/returnsetting',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@returnsetting',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@returnsetting',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.returnsetting',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.savereturnsetting' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/settings/taxes/savereturnsetting',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@returnsetting',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@returnsetting',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.savereturnsetting',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9ewCykq3FmugZlGi' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getsubcategories',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getsubcategories',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getsubcategories',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::9ewCykq3FmugZlGi',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::rHcjggzKLFTyiToP' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getassigneeajax',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getassigneeajax',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getassigneeajax',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::rHcjggzKLFTyiToP',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::oyHgj4NIBhUxE6mH' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getpartnerajax',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getpartnerajax',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getpartnerajax',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::oyHgj4NIBhUxE6mH',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::BGTh1ZUmOBuHfVuG' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/checkclientexist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@checkclientexist',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@checkclientexist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::BGTh1ZUmOBuHfVuG',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@index',
        'as' => 'admin.leads.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/detail/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@detail',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@detail',
        'as' => 'admin.leads.detail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.history' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/history/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@history',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@history',
        'as' => 'admin.leads.history',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@create',
        'as' => 'admin.leads.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/leads/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@store',
        'as' => 'admin.leads.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/{id}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@edit',
        'as' => 'admin.leads.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'admin/leads/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@update',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@update',
        'as' => 'admin.leads.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.patch' => 
    array (
      'methods' => 
      array (
        0 => 'PATCH',
      ),
      'uri' => 'admin/leads/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@update',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@update',
        'as' => 'admin.leads.patch',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.assign' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/leads/assign',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadAssignmentController@assign',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadAssignmentController@assign',
        'as' => 'admin.leads.assign',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.bulk_assign' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/leads/bulk-assign',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadAssignmentController@bulkAssign',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadAssignmentController@bulkAssign',
        'as' => 'admin.leads.bulk_assign',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.assignable_users' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/assignable-users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadAssignmentController@getAssignableUsers',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadAssignmentController@getAssignableUsers',
        'as' => 'admin.leads.assignable_users',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.convert' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/convert',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadConversionController@convertToClient',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadConversionController@convertToClient',
        'as' => 'admin.leads.convert',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.convert_single' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/leads/convert-single',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadConversionController@convertSingleLead',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadConversionController@convertSingleLead',
        'as' => 'admin.leads.convert_single',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.bulk_convert' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/leads/bulk-convert',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadConversionController@bulkConvertToClient',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadConversionController@bulkConvertToClient',
        'as' => 'admin.leads.bulk_convert',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.conversion_stats' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/conversion-stats',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadConversionController@getConversionStats',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadConversionController@getConversionStats',
        'as' => 'admin.leads.conversion_stats',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.followups.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/followups',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadFollowupController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadFollowupController@index',
        'as' => 'admin.leads.followups.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads/followups',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.followups.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/followups/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadFollowupController@myFollowups',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadFollowupController@myFollowups',
        'as' => 'admin.leads.followups.dashboard',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads/followups',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.followups.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/leads/followups',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadFollowupController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadFollowupController@store',
        'as' => 'admin.leads.followups.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads/followups',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.followups.complete' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/leads/followups/{id}/complete',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadFollowupController@complete',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadFollowupController@complete',
        'as' => 'admin.leads.followups.complete',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads/followups',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.followups.reschedule' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/leads/followups/{id}/reschedule',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadFollowupController@reschedule',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadFollowupController@reschedule',
        'as' => 'admin.leads.followups.reschedule',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads/followups',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.followups.cancel' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/leads/followups/{id}/cancel',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadFollowupController@cancel',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadFollowupController@cancel',
        'as' => 'admin.leads.followups.cancel',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads/followups',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.followups.get' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/{leadId}/followups',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadFollowupController@getLeadFollowups',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadFollowupController@getLeadFollowups',
        'as' => 'admin.leads.followups.get',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.analytics.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/analytics',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadAnalyticsController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadAnalyticsController@index',
        'as' => 'admin.leads.analytics.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads/analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.analytics.trends' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/analytics/trends',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadAnalyticsController@getTrends',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadAnalyticsController@getTrends',
        'as' => 'admin.leads.analytics.trends',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads/analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.analytics.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/analytics/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadAnalyticsController@export',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadAnalyticsController@export',
        'as' => 'admin.leads.analytics.export',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads/analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.analytics.compare' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/leads/analytics/compare-agents',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadAnalyticsController@compareAgents',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadAnalyticsController@compareAgents',
        'as' => 'admin.leads.analytics.compare',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads/analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.notes.delete' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/notes/delete/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@leaddeleteNotes',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@leaddeleteNotes',
        'as' => 'admin.leads.notes.delete',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.pin' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/pin/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@leadPin',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@leadPin',
        'as' => 'admin.leads.pin',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.get-notedetail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-notedetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@getnotedetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@getnotedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.get-notedetail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.email.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/email_templates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.email.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.email.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/email_templates/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.email.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.email.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/email_templates/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.email.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.edit_email_template' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/edit_email_template/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@editEmailTemplate',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@editEmailTemplate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.edit_email_template',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.edit_email_template.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/edit_email_template',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@editEmailTemplate',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@editEmailTemplate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.edit_email_template.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.edit_api' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/api-key',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@editapi',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@editapi',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.edit_api',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.edit_api.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/api-key',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@editapi',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@editapi',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.edit_api.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.clientsmatterslist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clientsmatterslist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@clientsmatterslist',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@clientsmatterslist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.clientsmatterslist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.clientsemaillist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clientsemaillist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@clientsemaillist',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@clientsemaillist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.clientsemaillist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.saveSection' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/save-section',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@saveSection',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@saveSection',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.saveSection',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.partnerEoiData' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/partner-eoi-data/{partnerId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@getPartnerEoiData',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@getPartnerEoiData',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.partnerEoiData',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.phone.sendOTP' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/phone/send-otp',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PhoneVerificationController@sendOTP',
        'controller' => 'App\\Http\\Controllers\\Admin\\PhoneVerificationController@sendOTP',
        'as' => 'clients.phone.sendOTP',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/clients/phone',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.phone.verifyOTP' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/phone/verify-otp',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PhoneVerificationController@verifyOTP',
        'controller' => 'App\\Http\\Controllers\\Admin\\PhoneVerificationController@verifyOTP',
        'as' => 'clients.phone.verifyOTP',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/clients/phone',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.phone.resendOTP' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/phone/resend-otp',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PhoneVerificationController@resendOTP',
        'controller' => 'App\\Http\\Controllers\\Admin\\PhoneVerificationController@resendOTP',
        'as' => 'clients.phone.resendOTP',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/clients/phone',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.phone.status' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/phone/status/{contactId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PhoneVerificationController@getStatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\PhoneVerificationController@getStatus',
        'as' => 'clients.phone.status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/clients/phone',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.email.sendVerification' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/email/send-verification',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailVerificationController@sendVerificationEmail',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailVerificationController@sendVerificationEmail',
        'as' => 'clients.email.sendVerification',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/clients/email',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.email.resendVerification' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/email/resend-verification',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailVerificationController@resendVerificationEmail',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailVerificationController@resendVerificationEmail',
        'as' => 'clients.email.resendVerification',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/clients/email',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.email.status' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/email/status/{emailId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailVerificationController@getStatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailVerificationController@getStatus',
        'as' => 'clients.email.status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/clients/email',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::gZwvuREmMnAY6rpE' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/followup/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@followupstore',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@followupstore',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::gZwvuREmMnAY6rpE',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::lWdSAXze07iPDAuM' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/followup/retagfollowup',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@retagfollowup',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@retagfollowup',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::lWdSAXze07iPDAuM',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::jOPrF3TgHGDYQ6gN' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/changetype/{id}/{type}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@changetype',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@changetype',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::jOPrF3TgHGDYQ6gN',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::xOj5Id7lBT50gPyg' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/document/download/pdf/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@downloadpdf',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@downloadpdf',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::xOj5Id7lBT50gPyg',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::yvsiY7FzzKxFKfp2' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/removetag',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@removetag',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@removetag',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::yvsiY7FzzKxFKfp2',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/detail/{client_id}/{client_unique_matter_ref_no?}/{tab?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@detail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@detail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.detail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getrecipients' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/get-recipients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getrecipients',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getrecipients',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getrecipients',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getonlyclientrecipients' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/get-onlyclientrecipients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getonlyclientrecipients',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getonlyclientrecipients',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getonlyclientrecipients',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getallclients' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/get-allclients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getallclients',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getallclients',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getallclients',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::nrSzRbSCPfO4gxF4' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/change_assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@change_assignee',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@change_assignee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::nrSzRbSCPfO4gxF4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.gettemplates' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-templates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@gettemplates',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@gettemplates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.gettemplates',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.sendmail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/sendmail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@sendmail',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@sendmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.sendmail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::3aNeCto0FLEyAP1j' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/upload-mail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@uploadmail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@uploadmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::3aNeCto0FLEyAP1j',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::z5zWkNmPtElFi4we' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/upload-fetch-mail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@uploadfetchmail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@uploadfetchmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::z5zWkNmPtElFi4we',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ItbeUIEkw989TuGL' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/upload-sent-fetch-mail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@uploadsentfetchmail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@uploadsentfetchmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::ItbeUIEkw989TuGL',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.reassiginboxemail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/reassiginboxemail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@reassiginboxemail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@reassiginboxemail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.reassiginboxemail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.reassigsentemail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/reassigsentemail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@reassigsentemail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@reassigsentemail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.reassigsentemail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.listAllMattersWRTSelClient' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/listAllMattersWRTSelClient',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@listAllMattersWRTSelClient',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@listAllMattersWRTSelClient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.listAllMattersWRTSelClient',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.updatemailreadbit' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/updatemailreadbit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@updatemailreadbit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@updatemailreadbit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.updatemailreadbit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.filter.emails' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/filter-emails',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@filterEmails',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@filterEmails',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.filter.emails',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.filter.sentmails' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/filter-sentemails',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@filterSentEmails',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@filterSentEmails',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.filter.sentmails',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.mail.enhance' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/mail/enhance',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@enhanceMessage',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@enhanceMessage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.mail.enhance',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.createnote' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/create-note',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@createnote',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@createnote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.createnote',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.updateNoteDatetime' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update-note-datetime',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@updateNoteDatetime',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@updateNoteDatetime',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.updateNoteDatetime',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getnotedetail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getnotedetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@getnotedetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@getnotedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getnotedetail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.deletenote' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/deletenote',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@deletenote',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@deletenote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.deletenote',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Ela76R7BgDcq0Vzc' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/viewnotedetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@viewnotedetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@viewnotedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Ela76R7BgDcq0Vzc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Xszy501Z7bSTTTXN' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/viewapplicationnote',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@viewapplicationnote',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@viewapplicationnote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Xszy501Z7bSTTTXN',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::UPdNLXoJIDUO6Vw6' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/saveprevvisa',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@saveprevvisa',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@saveprevvisa',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::UPdNLXoJIDUO6Vw6',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::bEtJvqUf54njC1nP' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/saveonlineprimaryform',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@saveonlineform',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@saveonlineform',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::bEtJvqUf54njC1nP',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Fz6U3eweh40IR3mY' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/saveonlinesecform',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@saveonlineform',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@saveonlineform',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Fz6U3eweh40IR3mY',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::EiM4wGQ98gyivfG6' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/saveonlinechildform',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@saveonlineform',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@saveonlineform',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::EiM4wGQ98gyivfG6',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getnotes' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-notes',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@getnotes',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@getnotes',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getnotes',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YpCYQlsXo5fMS5p0' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/pinnote',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@pinnote',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientNotesController@pinnote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::YpCYQlsXo5fMS5p0',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.convertActivityToNote' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/convert-activity-to-note',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@convertActivityToNote',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@convertActivityToNote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.convertActivityToNote',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.archived' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/archived',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@archived',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@archived',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.archived',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.updateclientstatus' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/change-client-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@updateclientstatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@updateclientstatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.updateclientstatus',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.activities' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-activities',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@activities',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@activities',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.activities',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.deletecostagreement' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/deletecostagreement',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@deletecostagreement',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@deletecostagreement',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.deletecostagreement',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.deleteactivitylog' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/deleteactivitylog',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@deleteactivitylog',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@deleteactivitylog',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.deleteactivitylog',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.notpickedcall' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/not-picked-call',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@notpickedcall',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@notpickedcall',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.notpickedcall',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::DEwLyVJzWqf2Aojl' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/pinactivitylog',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@pinactivitylog',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@pinactivitylog',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::DEwLyVJzWqf2Aojl',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::lxS99ZcseKT5qcUb' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/interested-service',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@interestedService',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@interestedService',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::lxS99ZcseKT5qcUb',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::LyFFCKtmPM0L7hs7' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/edit-interested-service',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@editinterestedService',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@editinterestedService',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::LyFFCKtmPM0L7hs7',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::CfLn1fmpu0dGlUfK' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-services',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getServices',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getServices',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::CfLn1fmpu0dGlUfK',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8thN4iANijZ0zEUA' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/servicesavefee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@servicesavefee',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@servicesavefee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::8thN4iANijZ0zEUA',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::UeOpnQXRtYgIi5jo' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getintrestedservice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getintrestedservice',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getintrestedservice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::UeOpnQXRtYgIi5jo',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::16krqIJdIVsstXAA' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getintrestedserviceedit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getintrestedserviceedit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getintrestedserviceedit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::16krqIJdIVsstXAA',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getapplicationlists' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-application-lists',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getapplicationlists',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getapplicationlists',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getapplicationlists',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.saveapplication' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/saveapplication',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveapplication',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveapplication',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.saveapplication',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.convertapplication' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/convertapplication',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@convertapplication',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@convertapplication',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.convertapplication',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.deleteservices' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/deleteservices',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@deleteservices',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@deleteservices',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.deleteservices',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::qH3htWKSq19Eeg4x' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/savetoapplication',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@savetoapplication',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@savetoapplication',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::qH3htWKSq19Eeg4x',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::4j9lCuXbWH966d9l' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/client/createservicetaken',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@createservicetaken',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@createservicetaken',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::4j9lCuXbWH966d9l',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::RadZGeHEoRGpk34e' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/client/removeservicetaken',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@removeservicetaken',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@removeservicetaken',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::RadZGeHEoRGpk34e',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::yK0MoWqkW7PLQNhT' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/client/getservicetaken',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getservicetaken',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getservicetaken',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::yK0MoWqkW7PLQNhT',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.documents.addedudocchecklist' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documents/add-edu-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@addedudocchecklist',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@addedudocchecklist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.documents.addedudocchecklist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.documents.uploadedudocument' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documents/upload-edu-document',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@uploadedudocument',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@uploadedudocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.documents.uploadedudocument',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.documents.addvisadocchecklist' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documents/add-visa-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@addvisadocchecklist',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@addvisadocchecklist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.documents.addvisadocchecklist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.documents.uploadvisadocument' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documents/upload-visa-document',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@uploadvisadocument',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@uploadvisadocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.documents.uploadvisadocument',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.documents.renamedoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documents/rename',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@renamedoc',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@renamedoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.documents.renamedoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.documents.deletedocs' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/documents/delete',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@deletedocs',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@deletedocs',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.documents.deletedocs',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.documents.getvisachecklist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/documents/get-visa-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@getvisachecklist',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@getvisachecklist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.documents.getvisachecklist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.documents.notuseddoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documents/not-used',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@notuseddoc',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@notuseddoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.documents.notuseddoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.documents.renamechecklistdoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documents/rename-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@renamechecklistdoc',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@renamechecklistdoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.documents.renamechecklistdoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.documents.backtodoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documents/back-to-doc',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@backtodoc',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@backtodoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.documents.backtodoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.documents.download' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documents/download',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@download_document',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@download_document',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.documents.download',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.documents.addPersonalDocCategory' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documents/add-personal-category',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@addPersonalDocCategory',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@addPersonalDocCategory',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.documents.addPersonalDocCategory',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.documents.updatePersonalDocCategory' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documents/update-personal-category',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@updatePersonalDocCategory',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@updatePersonalDocCategory',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.documents.updatePersonalDocCategory',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.documents.addVisaDocCategory' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documents/add-visa-category',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@addVisaDocCategory',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@addVisaDocCategory',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.documents.addVisaDocCategory',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.documents.updateVisaDocCategory' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documents/update-visa-category',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@updateVisaDocCategory',
        'controller' => 'App\\Http\\Controllers\\Admin\\Clients\\ClientDocumentsController@updateVisaDocCategory',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.documents.updateVisaDocCategory',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.eoi-roi.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/{admin}/eoi-roi',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientEoiRoiController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientEoiRoiController@index',
        'as' => 'admin.clients.eoi-roi.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/clients/{admin}/eoi-roi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.eoi-roi.calculatePoints' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/{admin}/eoi-roi/calculate-points',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientEoiRoiController@calculatePoints',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientEoiRoiController@calculatePoints',
        'as' => 'admin.clients.eoi-roi.calculatePoints',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/clients/{admin}/eoi-roi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.eoi-roi.upsert' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/{admin}/eoi-roi',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientEoiRoiController@upsert',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientEoiRoiController@upsert',
        'as' => 'admin.clients.eoi-roi.upsert',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/clients/{admin}/eoi-roi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.eoi-roi.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/{admin}/eoi-roi/{eoiReference}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientEoiRoiController@show',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientEoiRoiController@show',
        'as' => 'admin.clients.eoi-roi.show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/clients/{admin}/eoi-roi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.eoi-roi.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'admin/clients/{admin}/eoi-roi/{eoiReference}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientEoiRoiController@destroy',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientEoiRoiController@destroy',
        'as' => 'admin.clients.eoi-roi.destroy',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/clients/{admin}/eoi-roi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.eoi-roi.revealPassword' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/{admin}/eoi-roi/{eoiReference}/reveal-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientEoiRoiController@revealPassword',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientEoiRoiController@revealPassword',
        'as' => 'admin.clients.eoi-roi.revealPassword',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/clients/{admin}/eoi-roi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.saveaccountreport' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/saveaccountreport/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveaccountreport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveaccountreport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.saveaccountreport',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.saveaccountreport.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/saveaccountreport',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveaccountreport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveaccountreport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.saveaccountreport.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.saveinvoicereport' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/saveinvoicereport/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveinvoicereport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveinvoicereport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.saveinvoicereport',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.saveinvoicereport.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/saveinvoicereport',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveinvoicereport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveinvoicereport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.saveinvoicereport.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.saveadjustinvoicereport' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/saveadjustinvoicereport/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveadjustinvoicereport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveadjustinvoicereport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.saveadjustinvoicereport',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.saveadjustinvoicereport.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/saveadjustinvoicereport',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveadjustinvoicereport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveadjustinvoicereport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.saveadjustinvoicereport.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.saveofficereport' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/saveofficereport/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveofficereport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveofficereport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.saveofficereport',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.saveofficereport.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/saveofficereport',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveofficereport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveofficereport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.saveofficereport.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.savejournalreport' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/savejournalreport/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@savejournalreport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@savejournalreport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.savejournalreport',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.savejournalreport.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/savejournalreport',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@savejournalreport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@savejournalreport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.savejournalreport.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.isAnyInvoiceNoExistInDB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/isAnyInvoiceNoExistInDB',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@isAnyInvoiceNoExistInDB',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@isAnyInvoiceNoExistInDB',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.isAnyInvoiceNoExistInDB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.listOfInvoice' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/listOfInvoice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@listOfInvoice',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@listOfInvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.listOfInvoice',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getTopReceiptValInDB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/getTopReceiptValInDB',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getTopReceiptValInDB',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getTopReceiptValInDB',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getTopReceiptValInDB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getInfoByReceiptId' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/getInfoByReceiptId',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getInfoByReceiptId',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getInfoByReceiptId',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getInfoByReceiptId',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::XLUnE5c8uz7U2jal' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/genInvoice/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@genInvoice',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@genInvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::XLUnE5c8uz7U2jal',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.sendToHubdoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/sendToHubdoc/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@sendToHubdoc',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@sendToHubdoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.sendToHubdoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.checkHubdocStatus' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/checkHubdocStatus/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@checkHubdocStatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@checkHubdocStatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.checkHubdocStatus',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::EYxbHxlrbAoToS9l' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/printPreview/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@printPreview',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@printPreview',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::EYxbHxlrbAoToS9l',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getTopInvoiceNoFromDB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/getTopInvoiceNoFromDB',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getTopInvoiceNoFromDB',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getTopInvoiceNoFromDB',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getTopInvoiceNoFromDB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.clientLedgerBalanceAmount' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/clientLedgerBalanceAmount',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@clientLedgerBalanceAmount',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@clientLedgerBalanceAmount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.clientLedgerBalanceAmount',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.invoicelist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/invoicelist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@invoicelist',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@invoicelist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.invoicelist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'client.void_invoice' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/void_invoice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@void_invoice',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@void_invoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'client.void_invoice',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.clientreceiptlist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/clientreceiptlist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@clientreceiptlist',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@clientreceiptlist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.clientreceiptlist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.officereceiptlist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/officereceiptlist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@officereceiptlist',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@officereceiptlist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.officereceiptlist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.journalreceiptlist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/journalreceiptlist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@journalreceiptlist',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@journalreceiptlist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.journalreceiptlist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'client.validate_receipt' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/validate_receipt',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@validate_receipt',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@validate_receipt',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'client.validate_receipt',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::TRqmdzPVErBTzMWy' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/delete_receipt',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@delete_receipt',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@delete_receipt',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::TRqmdzPVErBTzMWy',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::VDgTKd0mQCTzY61Z' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/genClientFundLedgerInvoice/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@genClientFundLedgerInvoice',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@genClientFundLedgerInvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::VDgTKd0mQCTzY61Z',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::gHcJGWLv0CrqcPij' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/genofficereceiptInvoice/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@genofficereceiptInvoice',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@genofficereceiptInvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::gHcJGWLv0CrqcPij',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.update-client-funds-ledger' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update-client-funds-ledger',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@updateClientFundsLedger',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@updateClientFundsLedger',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.update-client-funds-ledger',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.invoiceamount' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/invoiceamount',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getInvoiceAmount',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getInvoiceAmount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.invoiceamount',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.updateAddress' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/update-address',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@updateAddress',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@updateAddress',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.updateAddress',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.searchAddressFull' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/search-address-full',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@searchAddressFull',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@searchAddressFull',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.searchAddressFull',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getPlaceDetails' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/get-place-details',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@getPlaceDetails',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@getPlaceDetails',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getPlaceDetails',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::33hZgO9qlrUJsxGo' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/address_auto_populate',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@address_auto_populate',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@address_auto_populate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::33hZgO9qlrUJsxGo',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::bUgdSuTLthcL90aE' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/fetchClientContactNo',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@fetchClientContactNo',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@fetchClientContactNo',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::bUgdSuTLthcL90aE',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.clientdetailsinfo' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/clientdetailsinfo/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@clientdetailsinfo',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@clientdetailsinfo',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.clientdetailsinfo',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.clientdetailsinfo.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/clientdetailsinfo',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@clientdetailsinfo',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@clientdetailsinfo',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.clientdetailsinfo.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.getVisaTypes' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/admin/get-visa-types',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@getVisaTypes',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@getVisaTypes',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.getVisaTypes',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.getCountries' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/admin/get-countries',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@getCountries',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@getCountries',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.getCountries',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.updateOccupation' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/updateOccupation',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@updateOccupation',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@updateOccupation',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.updateOccupation',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.updateOccupation' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/leads/updateOccupation',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@updateOccupation',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@updateOccupation',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.leads.updateOccupation',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.searchPartner' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/admin/clients/search-partner',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@searchPartner',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@searchPartner',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.searchPartner',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.searchPartnerTest' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/admin/clients/search-partner-test',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@searchPartnerTest',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@searchPartnerTest',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.searchPartnerTest',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.testBidirectional' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/admin/clients/test-bidirectional',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@testBidirectionalRemoval',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@testBidirectionalRemoval',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.testBidirectional',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.saveRelationship' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/admin/clients/save-relationship',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@saveRelationship',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@saveRelationship',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.saveRelationship',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.generateagreement' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/generateagreement',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@generateagreement',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@generateagreement',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.generateagreement',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getMigrationAgentDetail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/getMigrationAgentDetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getMigrationAgentDetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getMigrationAgentDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getMigrationAgentDetail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getVisaAggreementMigrationAgentDetail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/getVisaAggreementMigrationAgentDetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getVisaAggreementMigrationAgentDetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getVisaAggreementMigrationAgentDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getVisaAggreementMigrationAgentDetail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getCostAssignmentMigrationAgentDetail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/getCostAssignmentMigrationAgentDetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getCostAssignmentMigrationAgentDetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getCostAssignmentMigrationAgentDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getCostAssignmentMigrationAgentDetail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.savecostassignment' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/savecostassignment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@savecostassignment',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@savecostassignment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.savecostassignment',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Q7ulBe59JrVzw8ma' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/check-cost-assignment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@checkCostAssignment',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@checkCostAssignment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Q7ulBe59JrVzw8ma',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.savecostassignmentlead' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/savecostassignmentlead',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@savecostassignmentlead',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@savecostassignmentlead',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.savecostassignmentlead',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getCostAssignmentMigrationAgentDetailLead' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/getCostAssignmentMigrationAgentDetailLead',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getCostAssignmentMigrationAgentDetailLead',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getCostAssignmentMigrationAgentDetailLead',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'clients.getCostAssignmentMigrationAgentDetailLead',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.uploadAgreement' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/{admin}/upload-agreement',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@uploadAgreement',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@uploadAgreement',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.uploadAgreement',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'forms.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/admin/forms',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Form956Controller@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\Form956Controller@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'forms.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'forms.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/admin/forms/{form}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Form956Controller@show',
        'controller' => 'App\\Http\\Controllers\\Admin\\Form956Controller@show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'forms.show',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'forms.preview' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/forms/{form}/preview',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Form956Controller@previewPdf',
        'controller' => 'App\\Http\\Controllers\\Admin\\Form956Controller@previewPdf',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'forms.preview',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'forms.pdf' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/forms/{form}/pdf',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Form956Controller@generatePdf',
        'controller' => 'App\\Http\\Controllers\\Admin\\Form956Controller@generatePdf',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'forms.pdf',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getmattertemplates' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-matter-templates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getmattertemplates',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getmattertemplates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getmattertemplates',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getClientMatters' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-client-matters/{clientId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getClientMatters',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getClientMatters',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getClientMatters',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::bwj9uMRB8Wbxssw4' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/fetchClientMatterAssignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@fetchClientMatterAssignee',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@fetchClientMatterAssignee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::bwj9uMRB8Wbxssw4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::38ZhhM5z2UlO2Y8M' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/updateClientMatterAssignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@updateClientMatterAssignee',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPersonalDetailsController@updateClientMatterAssignee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::38ZhhM5z2UlO2Y8M',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.upload_checklists.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/upload-checklists',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UploadChecklistController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\UploadChecklistController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.upload_checklists.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.upload_checklists.matter' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/upload-checklists/matter/{matterId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UploadChecklistController@showByMatter',
        'controller' => 'App\\Http\\Controllers\\Admin\\UploadChecklistController@showByMatter',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.upload_checklists.matter',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.upload_checklistsupload' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/upload-checklists/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UploadChecklistController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\UploadChecklistController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.upload_checklistsupload',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YVTl0Jwr79DH6r8d' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/personalfollowup/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@personalfollowup',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@personalfollowup',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::YVTl0Jwr79DH6r8d',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::HtU4DyMAAyihO7Jo' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/updatefollowup/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@updatefollowup',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@updatefollowup',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::HtU4DyMAAyihO7Jo',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::qiSJOpAIhNcaU8F3' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/reassignfollowup/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@reassignfollowupstore',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@reassignfollowupstore',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::qiSJOpAIhNcaU8F3',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.updatesessioncompleted' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/update-session-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@updatesessioncompleted',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@updatesessioncompleted',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.updatesessioncompleted',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getAllUser' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/getAllUser',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getAllUser',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getAllUser',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getAllUser',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.toggleClientPortal' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/toggle-client-portal',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientPortalController@toggleClientPortal',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientPortalController@toggleClientPortal',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.toggleClientPortal',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.anzsco.search' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/anzsco/search',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@search',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@search',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.anzsco.search',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'check.email' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/check-email',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@checkEmail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@checkEmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'check.email',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'check.phone' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/check.phone',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@checkContact',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@checkContact',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'check.phone',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Y4kUFOykwX1F5aJ0' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/save_tag',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@save_tag',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@save_tag',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Y4kUFOykwX1F5aJ0',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'references.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/save-references',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@savereferences',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@savereferences',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'references.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'check.star.client' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/check-star-client',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@checkStarClient',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@checkStarClient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'check.star.client',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'client.merge_records' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/merge_records',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@merge_records',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@merge_records',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'client.merge_records',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.send-webhook' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/send-webhook',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@sendToWebhook',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@sendToWebhook',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.send-webhook',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::IEKH9RRyaKAobQq8' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/fetch-visa_expiry_messages',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchvisaexpirymessages',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchvisaexpirymessages',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::IEKH9RRyaKAobQq8',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.email.verify' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/verify-email/{token}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailVerificationController@verifyEmail',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailVerificationController@verifyEmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.email.verify',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.applications.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/applications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.applications.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.applications.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/applications/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.applications.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.applications.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/applications/detail/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@detail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@detail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.applications.detail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.applications.import' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/applications-import',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@import',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@import',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.applications.import',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::GepiPhwIQTFn3yyN' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getapplicationdetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplicationdetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplicationdetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::GepiPhwIQTFn3yyN',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::naRYbelwPP9xCBVs' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/load-application-insert-update-data',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@loadApplicationInsertUpdateData',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@loadApplicationInsertUpdateData',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::naRYbelwPP9xCBVs',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::0R3l1mTg0MxOXU1v' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/updatestage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updatestage',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updatestage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::0R3l1mTg0MxOXU1v',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::urFyOagwFjnautbq' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/completestage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@completestage',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@completestage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::urFyOagwFjnautbq',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::x8CTRivEpLqkGcxC' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/updatebackstage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updatebackstage',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updatebackstage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::x8CTRivEpLqkGcxC',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Dx7tHSBLsdQPHNWd' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-applications-logs',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplicationslogs',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplicationslogs',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Dx7tHSBLsdQPHNWd',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::VSpYuVRC4B32T0oS' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-applications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplications',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplications',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::VSpYuVRC4B32T0oS',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::vY5i8K0mm82XsFol' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/discontinue_application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@discontinue_application',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@discontinue_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::vY5i8K0mm82XsFol',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::CCuUi0gRd75hcddx' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/revert_application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@revert_application',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@revert_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::CCuUi0gRd75hcddx',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ynDZX17gYhTu3Lq9' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/create-app-note',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@addNote',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@addNote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::ynDZX17gYhTu3Lq9',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::itxktcm3RwYOEu90' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getapplicationnotes',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplicationnotes',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplicationnotes',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::itxktcm3RwYOEu90',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::rGXXGZobx8AQ4exp' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/application-sendmail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@applicationsendmail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@applicationsendmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::rGXXGZobx8AQ4exp',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::zGS5bXZVLRjMGSED' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/application/updateintake',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updateintake',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updateintake',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::zGS5bXZVLRjMGSED',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::TrfptvDdOuQv4mdu' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/application/updatedates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updatedates',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updatedates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::TrfptvDdOuQv4mdu',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::zRt7SdjlCVJqtWds' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/application/updateexpectwin',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updateexpectwin',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updateexpectwin',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::zRt7SdjlCVJqtWds',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::SH5R6w9d7jrmnK7M' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/application/getapplicationbycid',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplicationbycid',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplicationbycid',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::SH5R6w9d7jrmnK7M',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YCNIbj0JmK6nbsnh' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/application/spagent_application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@spagent_application',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@spagent_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::YCNIbj0JmK6nbsnh',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::EPDP2V906oe4pqQb' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/application/sbagent_application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@sbagent_application',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@sbagent_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::EPDP2V906oe4pqQb',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::o0t1MTXeIh54SyOR' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/application/application_ownership',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@application_ownership',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@application_ownership',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::o0t1MTXeIh54SyOR',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::pRh6lYJvaiC07Zor' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/superagent',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@superagent',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@superagent',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::pRh6lYJvaiC07Zor',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::JuceiNXdAdGa9YR8' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/subagent',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@subagent',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@subagent',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::JuceiNXdAdGa9YR8',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5898wMY7UWaMFu3Y' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/applicationsavefee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@applicationsavefee',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@applicationsavefee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::5898wMY7UWaMFu3Y',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::TLGWh2efhNCdoGbO' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/application/export/pdf/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@exportapplicationpdf',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@exportapplicationpdf',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::TLGWh2efhNCdoGbO',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ygr9QlPxesxxHP5k' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/add-checklists',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@addchecklists',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@addchecklists',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::ygr9QlPxesxxHP5k',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::yx91aaDpHMygF9vS' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/application/checklistupload',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@checklistupload',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@checklistupload',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::yx91aaDpHMygF9vS',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::klDXooLDw9icZXn5' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/deleteapplicationdocs',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@deleteapplicationdocs',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@deleteapplicationdocs',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::klDXooLDw9icZXn5',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::T7Cz3r2wTzIMjzIy' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/application/publishdoc',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@publishdoc',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@publishdoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::T7Cz3r2wTzIMjzIy',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.migration.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/migration',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@migrationindex',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@migrationindex',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.migration.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.officevisits.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/office-visits',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.officevisits.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.officevisits.waiting' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/office-visits/waiting',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@waiting',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@waiting',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.officevisits.waiting',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.officevisits.attending' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/office-visits/attending',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@attending',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@attending',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.officevisits.attending',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.officevisits.completed' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/office-visits/completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@completed',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@completed',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.officevisits.completed',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.officevisits.archived' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/office-visits/archived',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@archived',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@archived',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.officevisits.archived',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.officevisits.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/office-visits/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.officevisits.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::UKCPTBgEded79VTB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/checkin',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@checkin',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@checkin',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::UKCPTBgEded79VTB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::2mlTUz2xQ6jX43we' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-checkin-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@getcheckin',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@getcheckin',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::2mlTUz2xQ6jX43we',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::l8RZBOo6m4QZNAXX' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update_visit_purpose',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@update_visit_purpose',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@update_visit_purpose',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::l8RZBOo6m4QZNAXX',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::jjxT3fFOpKLqqOvn' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update_visit_comment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@update_visit_comment',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@update_visit_comment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::jjxT3fFOpKLqqOvn',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::E0wleZrK7BNhFDOa' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/attend_session',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@attend_session',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@attend_session',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::E0wleZrK7BNhFDOa',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::DB54YSDFwAXNQIuz' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/complete_session',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@complete_session',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@complete_session',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::DB54YSDFwAXNQIuz',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::APmbvu7Y2ghiR8Qd' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/office-visits/change_assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@change_assignee',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@change_assignee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::APmbvu7Y2ghiR8Qd',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/appointments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'appointments.index',
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/appointments/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'appointments.create',
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/appointments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'appointments.store',
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/appointments/{appointment}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'appointments.show',
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@show',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/appointments/{appointment}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'appointments.edit',
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'admin/appointments/{appointment}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'appointments.update',
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@update',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'admin/appointments/{appointment}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'appointments.destroy',
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@destroy',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@destroy',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments-adelaide' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/appointments-adelaide',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@appointmentsAdelaide',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@appointmentsAdelaide',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'appointments-adelaide',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::gFiPSxSsWNyQpXel' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/deleteappointment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@deleteappointment',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@deleteappointment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::gFiPSxSsWNyQpXel',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::fykTFaCQVQcSZ0r3' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/add-appointment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@addAppointment',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@addAppointment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::fykTFaCQVQcSZ0r3',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9zodyFlC73QEilFR' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/add-appointment-book',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@addAppointmentBook',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@addAppointmentBook',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::9zodyFlC73QEilFR',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::zVI2T1zOShUBiVOs' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/editappointment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@editappointment',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@editappointment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::zVI2T1zOShUBiVOs',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::eTrc6VgkfhXNNXgc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/updatefollowupschedule',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@updatefollowupschedule',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@updatefollowupschedule',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::eTrc6VgkfhXNNXgc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::roTP3zmhwXmy02Zs' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/updateappointmentstatus/{status}/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@updateappointmentstatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@updateappointmentstatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::roTP3zmhwXmy02Zs',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9cCjE6Zvagq9pHJu' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update_appointment_status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@update_appointment_status',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@update_appointment_status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::9cCjE6Zvagq9pHJu',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::hCAjCqXki3keit9i' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update_appointment_priority',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@update_appointment_priority',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@update_appointment_priority',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::hCAjCqXki3keit9i',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::iz4CbA4YBdyVimx4' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update_apppointment_comment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@update_apppointment_comment',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@update_apppointment_comment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::iz4CbA4YBdyVimx4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::7i40W2wvCrl9Qm0i' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update_apppointment_description',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@update_apppointment_description',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@update_apppointment_description',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::7i40W2wvCrl9Qm0i',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::wwLHq6LIsevE7KDo' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-appointments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@getAppointments',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@getAppointments',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::wwLHq6LIsevE7KDo',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::rPekgIaAWXMcFokN' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getAppointmentdetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@getAppointmentdetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@getAppointmentdetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::rPekgIaAWXMcFokN',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::sDgQ3k051x9tY1oB' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-assigne-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@assignedetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@assignedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::sDgQ3k051x9tY1oB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::MY3UIorbtyz1WyxU' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/change_assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@change_assignee',
        'controller' => 'App\\Http\\Controllers\\Admin\\AppointmentsController@change_assignee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::MY3UIorbtyz1WyxU',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'getdatetimebackend' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/getdatetimebackend',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\HomeController@getdatetimebackend',
        'controller' => 'App\\Http\\Controllers\\HomeController@getdatetimebackend',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'getdatetimebackend',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'getdisableddatetime' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/getdisableddatetime',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\HomeController@getdisableddatetime',
        'controller' => 'App\\Http\\Controllers\\HomeController@getdisableddatetime',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'getdisableddatetime',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/booking/appointments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@index',
        'as' => 'booking.appointments.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/booking/appointments/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@show',
        'controller' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@show',
        'as' => 'booking.appointments.show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.json' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/booking/appointments/{id}/json',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@getAppointmentJson',
        'controller' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@getAppointmentJson',
        'as' => 'booking.appointments.json',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.calendar' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/booking/calendar/{type}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@calendar',
        'controller' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@calendar',
        'as' => 'booking.appointments.calendar',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'type' => 'paid|jrp|education|tourist|adelaide',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.update-status' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/booking/appointments/{id}/update-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@updateStatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@updateStatus',
        'as' => 'booking.appointments.update-status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.update-consultant' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/booking/appointments/{id}/update-consultant',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@updateConsultant',
        'controller' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@updateConsultant',
        'as' => 'booking.appointments.update-consultant',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.add-note' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/booking/appointments/{id}/add-note',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@addNote',
        'controller' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@addNote',
        'as' => 'booking.appointments.add-note',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.update-followup' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/booking/appointments/{id}/update-followup',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@updateFollowUp',
        'controller' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@updateFollowUp',
        'as' => 'booking.appointments.update-followup',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.send-reminder' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/booking/appointments/{id}/send-reminder',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@sendReminder',
        'controller' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@sendReminder',
        'as' => 'booking.appointments.send-reminder',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.bulk-update-status' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/booking/appointments/bulk-update-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@bulkUpdateStatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@bulkUpdateStatus',
        'as' => 'booking.appointments.bulk-update-status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/booking/appointments/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@export',
        'controller' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@export',
        'as' => 'booking.appointments.export',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.sync.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/booking/sync/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@syncDashboard',
        'controller' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@syncDashboard',
        'as' => 'booking.sync.dashboard',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.sync.stats' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/booking/sync/stats',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@syncStats',
        'controller' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@syncStats',
        'as' => 'booking.sync.stats',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.sync.manual' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/booking/sync/manual',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'can:trigger-manual-sync',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@manualSync',
        'controller' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@manualSync',
        'as' => 'booking.sync.manual',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.api.appointments' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/booking/api/appointments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@getAppointments',
        'controller' => 'App\\Http\\Controllers\\Admin\\BookingAppointmentsController@getAppointments',
        'as' => 'booking.api.appointments',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.auditlogs.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/audit-logs',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AuditLogController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\AuditLogController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.auditlogs.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::m6ncriDYZ3Ld3kBA' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/fetch-notification',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchnotification',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchnotification',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::m6ncriDYZ3Ld3kBA',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::jDQmS5E0C3gx0gNu' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/fetch-messages',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchmessages',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchmessages',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::jDQmS5E0C3gx0gNu',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::dwAfA5fyEeCWXFOV' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/fetch-office-visit-notifications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchOfficeVisitNotifications',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchOfficeVisitNotifications',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::dwAfA5fyEeCWXFOV',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::GyfdphATR4KzfAGW' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/mark-notification-seen',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@markNotificationSeen',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@markNotificationSeen',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::GyfdphATR4KzfAGW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Po9QjsiMaJPeq5nq' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/check-checkin-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@checkCheckinStatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@checkCheckinStatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Po9QjsiMaJPeq5nq',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::eVOhN1VRF2ZmuHz3' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update-checkin-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@updateCheckinStatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@updateCheckinStatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::eVOhN1VRF2ZmuHz3',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::j0n6YDeo4gu05zph' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/all-notifications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@allnotification',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@allnotification',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::j0n6YDeo4gu05zph',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::2EKyJHvILvOWqga9' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/fetch-InPersonWaitingCount',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchInPersonWaitingCount',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchInPersonWaitingCount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::2EKyJHvILvOWqga9',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::GL3FHmiwWMmCprYa' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/fetch-TotalActivityCount',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchTotalActivityCount',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchTotalActivityCount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::GL3FHmiwWMmCprYa',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'assignee.index',
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/assignee/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'assignee.create',
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'assignee.store',
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/assignee/{assignee}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'assignee.show',
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@show',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/assignee/{assignee}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'assignee.edit',
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'admin/assignee/{assignee}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'assignee.update',
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@update',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'admin/assignee/{assignee}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'assignee.destroy',
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::HLdGWR5UhufK3Daj' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/assignee-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@completed',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@completed',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::HLdGWR5UhufK3Daj',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::yU8gqx30wQOPVXE7' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update-task-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@updatetaskcompleted',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@updatetaskcompleted',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::yU8gqx30wQOPVXE7',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Jh5OTFEbsuZHJZG2' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update-task-not-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@updatetasknotcompleted',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@updatetasknotcompleted',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Jh5OTFEbsuZHJZG2',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.assigned_by_me' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/assigned_by_me',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@assigned_by_me',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@assigned_by_me',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'assignee.assigned_by_me',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.assigned_to_me' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/assigned_to_me',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@assigned_to_me',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@assigned_to_me',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'assignee.assigned_to_me',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.destroy_by_me' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'admin/destroy_by_me/{note_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy_by_me',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy_by_me',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'assignee.destroy_by_me',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.destroy_to_me' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'admin/destroy_to_me/{note_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy_to_me',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy_to_me',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'assignee.destroy_to_me',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.action_completed' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/action_completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@action_completed',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@action_completed',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'assignee.action_completed',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.destroy_activity' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'admin/destroy_activity/{note_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy_activity',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy_activity',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'assignee.destroy_activity',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.destroy_complete_activity' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'admin/destroy_complete_activity/{note_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy_complete_activity',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy_complete_activity',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'assignee.destroy_complete_activity',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5hISzjh0jqCC1Hdh' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/is_email_unique',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@is_email_unique',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@is_email_unique',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::5hISzjh0jqCC1Hdh',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8nNg6NhO8Nchlok1' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/is_contactno_unique',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@is_contactno_unique',
        'controller' => 'App\\Http\\Controllers\\Admin\\Leads\\LeadController@is_contactno_unique',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::8nNg6NhO8Nchlok1',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::MxcFexmuxXvzqjtk' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/extenddeadlinedate',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@extenddeadlinedate',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@extenddeadlinedate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::MxcFexmuxXvzqjtk',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::L2gjNtfgoi0S1avq' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update-stage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@updateStage',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@updateStage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::L2gjNtfgoi0S1avq',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::F9W8EG9aDnr8p3mh' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/get_assignee_list',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@get_assignee_list',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@get_assignee_list',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::F9W8EG9aDnr8p3mh',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::dW0DZ0NARDGFCnmk' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update-task',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@updateTask',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@updateTask',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::dW0DZ0NARDGFCnmk',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'action.counts' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/action/counts',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@getActionCounts',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@getActionCounts',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'action.counts',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.action' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@action',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@action',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'assignee.action',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'action.list' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/action/list',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@getAction',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@getAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'action.list',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.documents.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/documents/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocumentController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocumentController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.documents.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.documents.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/documents/{id?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:167:"function($id = null) {
    if ($id) {
        return \\redirect()->route(\'admin.signatures.show\', $id);
    }
    return \\redirect()->route(\'admin.signatures.index\');
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000d010000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.documents.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.documents.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documents',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocumentController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocumentController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.documents.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.documents.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/documents/{id}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocumentController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocumentController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.documents.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.documents.update' => 
    array (
      'methods' => 
      array (
        0 => 'PATCH',
      ),
      'uri' => 'admin/documents/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocumentController@update',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocumentController@update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.documents.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.documents.submitSignatures' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documents/{document}/sign',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocumentController@submitSignatures',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocumentController@submitSignatures',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.documents.submitSignatures',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.documents.sendReminder' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documents/{document}/send-reminder',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocumentController@sendReminder',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocumentController@sendReminder',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.documents.sendReminder',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.documents.sendSigningLink' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documents/{document}/send-signing-link',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocumentController@sendSigningLink',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocumentController@sendSigningLink',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.documents.sendSigningLink',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.documents.showSignForm' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/documents/{document}/sign',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocumentController@showSignForm',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocumentController@showSignForm',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.documents.showSignForm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.documents.sign' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/sign/{id}/{token}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocumentController@sign',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocumentController@sign',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.documents.sign',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.documents.page' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/documents/{id}/page/{page}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocumentController@getPage',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocumentController@getPage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.documents.page',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.documents.download.signed' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/documents/{id}/download-signed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocumentController@downloadSigned',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocumentController@downloadSigned',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.documents.download.signed',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.documents.download_and_thankyou' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/documents/{id}/download-signed-and-thankyou',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocumentController@downloadSignedAndThankyou',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocumentController@downloadSignedAndThankyou',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.documents.download_and_thankyou',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.documents.thankyou' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/documents/thankyou/{id?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocumentController@thankyou',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocumentController@thankyou',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.documents.thankyou',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'test.signature' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/test-signature',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:51:"function () {
    return \\view(\'test-signature\');
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000d0f0000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'test.signature',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.doc-to-pdf.form' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/doc-to-pdf',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocToPdfController@showForm',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocToPdfController@showForm',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.doc-to-pdf.form',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.doc-to-pdf.convert' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/doc-to-pdf/convert',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocToPdfController@convertLocal',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocToPdfController@convertLocal',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.doc-to-pdf.convert',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.doc-to-pdf.test' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/doc-to-pdf/test',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocToPdfController@testLocalConversion',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocToPdfController@testLocalConversion',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.doc-to-pdf.test',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.doc-to-pdf.test-python' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/doc-to-pdf/test-python',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocToPdfController@testPythonConversion',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocToPdfController@testPythonConversion',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.doc-to-pdf.test-python',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.doc-to-pdf.debug' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/doc-to-pdf/debug',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocToPdfController@debugConfig',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocToPdfController@debugConfig',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.doc-to-pdf.debug',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.signatures.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/signatures',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/signatures',
        'where' => 
        array (
        ),
        'as' => 'admin.signatures.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.signatures.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/signatures/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/signatures',
        'where' => 
        array (
        ),
        'as' => 'admin.signatures.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.signatures.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/signatures',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/signatures',
        'where' => 
        array (
        ),
        'as' => 'admin.signatures.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.signatures.suggest-association' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/signatures/suggest-association',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@suggestAssociation',
        'controller' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@suggestAssociation',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/signatures',
        'where' => 
        array (
        ),
        'as' => 'admin.signatures.suggest-association',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.signatures.preview-email' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/signatures/preview-email',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@previewEmail',
        'controller' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@previewEmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/signatures',
        'where' => 
        array (
        ),
        'as' => 'admin.signatures.preview-email',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.signatures.bulk-archive' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/signatures/bulk-archive',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@bulkArchive',
        'controller' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@bulkArchive',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/signatures',
        'where' => 
        array (
        ),
        'as' => 'admin.signatures.bulk-archive',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.signatures.bulk-void' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/signatures/bulk-void',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@bulkVoid',
        'controller' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@bulkVoid',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/signatures',
        'where' => 
        array (
        ),
        'as' => 'admin.signatures.bulk-void',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.signatures.bulk-resend' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/signatures/bulk-resend',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@bulkResend',
        'controller' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@bulkResend',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/signatures',
        'where' => 
        array (
        ),
        'as' => 'admin.signatures.bulk-resend',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.signatures.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/signatures/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@show',
        'controller' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/signatures',
        'where' => 
        array (
        ),
        'as' => 'admin.signatures.show',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.signatures.reminder' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/signatures/{id}/reminder',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@sendReminder',
        'controller' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@sendReminder',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/signatures',
        'where' => 
        array (
        ),
        'as' => 'admin.signatures.reminder',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.signatures.send' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/signatures/{id}/send',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@sendForSignature',
        'controller' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@sendForSignature',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/signatures',
        'where' => 
        array (
        ),
        'as' => 'admin.signatures.send',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.signatures.copy-link' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/signatures/{id}/copy-link',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@copyLink',
        'controller' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@copyLink',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/signatures',
        'where' => 
        array (
        ),
        'as' => 'admin.signatures.copy-link',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.signatures.associate' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/signatures/{id}/associate',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@associate',
        'controller' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@associate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/signatures',
        'where' => 
        array (
        ),
        'as' => 'admin.signatures.associate',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.signatures.detach' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/signatures/{id}/detach',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@detach',
        'controller' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@detach',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/signatures',
        'where' => 
        array (
        ),
        'as' => 'admin.signatures.detach',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.matters' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/{id}/matters',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@getClientMatters',
        'controller' => 'App\\Http\\Controllers\\Admin\\SignatureDashboardController@getClientMatters',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.matters',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'debug.pdf.page' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'debug-pdf-page/{id}/{page}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:1363:"function($id, $page) {
    // Clear any output buffers to prevent corruption
    if (\\ob_get_level()) {
        \\ob_end_clean();
    }
    
    try {
        $document = \\App\\Models\\Document::findOrFail($id);
        $url = $document->myfile;
        
        if ($url && \\file_exists(\\storage_path(\'app/public/\' . $url))) {
            $filePath = \\storage_path(\'app/public/\' . $url);
            
            $pdfService = \\app(\\App\\Services\\PythonPDFService::class);
            if ($pdfService->isHealthy()) {
                $result = $pdfService->convertPageToImage($filePath, $page, 150);
                
                if ($result && ($result[\'success\'] ?? false)) {
                    $imageData = \\base64_decode(\\explode(\',\', $result[\'image_data\'])[1]);
                    
                    // Return raw binary response with proper headers
                    return \\response($imageData, 200, [
                        \'Content-Type\' => \'image/png\',
                        \'Content-Length\' => \\strlen($imageData),
                        \'Cache-Control\' => \'public, max-age=3600\',
                    ]);
                }
            }
        }
        
        return \\response()->json([\'error\' => \'Failed to generate image\'], 500);
    } catch (\\Exception $e) {
        return \\response()->json([\'error\' => $e->getMessage()], 500);
    }
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000b160000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'debug.pdf.page',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'public.documents.sign' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'sign/{id}/{token}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\PublicDocumentController@sign',
        'controller' => 'App\\Http\\Controllers\\PublicDocumentController@sign',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'public.documents.sign',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'public.documents.submitSignatures' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/{document}/sign',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\PublicDocumentController@submitSignatures',
        'controller' => 'App\\Http\\Controllers\\PublicDocumentController@submitSignatures',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'public.documents.submitSignatures',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'public.documents.page' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/{id}/page/{page}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\PublicDocumentController@getPage',
        'controller' => 'App\\Http\\Controllers\\PublicDocumentController@getPage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'public.documents.page',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'public.documents.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/{id?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\PublicDocumentController@index',
        'controller' => 'App\\Http\\Controllers\\PublicDocumentController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'public.documents.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'public.documents.download.signed' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/{id}/download-signed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\PublicDocumentController@downloadSigned',
        'controller' => 'App\\Http\\Controllers\\PublicDocumentController@downloadSigned',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'public.documents.download.signed',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'public.documents.download_and_thankyou' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/{id}/download-signed-and-thankyou',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\PublicDocumentController@downloadSignedAndThankyou',
        'controller' => 'App\\Http\\Controllers\\PublicDocumentController@downloadSignedAndThankyou',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'public.documents.download_and_thankyou',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'public.documents.thankyou' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/thankyou/{id?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\PublicDocumentController@thankyou',
        'controller' => 'App\\Http\\Controllers\\PublicDocumentController@thankyou',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'public.documents.thankyou',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'public.documents.sendReminder' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/{document}/send-reminder',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\PublicDocumentController@sendReminder',
        'controller' => 'App\\Http\\Controllers\\PublicDocumentController@sendReminder',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'public.documents.sendReminder',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'webhooks.sms.twilio.status' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'webhooks/sms/twilio/status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsWebhookController@twilioStatus',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsWebhookController@twilioStatus',
        'as' => 'webhooks.sms.twilio.status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/webhooks/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'webhooks.sms.twilio.incoming' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'webhooks/sms/twilio/incoming',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsWebhookController@twilioIncoming',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsWebhookController@twilioIncoming',
        'as' => 'webhooks.sms.twilio.incoming',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/webhooks/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'webhooks.sms.cellcast.status' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'webhooks/sms/cellcast/status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsWebhookController@cellcastStatus',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsWebhookController@cellcastStatus',
        'as' => 'webhooks.sms.cellcast.status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/webhooks/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'webhooks.sms.cellcast.incoming' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'webhooks/sms/cellcast/incoming',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsWebhookController@cellcastIncoming',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsWebhookController@cellcastIncoming',
        'as' => 'webhooks.sms.cellcast.incoming',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/webhooks/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::xbU8ygw7cGcpWcJm' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'test-messaging',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:336:"function () {
    // Create a test user session for development
    if (!\\auth()->check()) {
        // Use the Super1 Admin1 account for testing
        $admin = \\App\\Models\\Admin::find(1); // Super1 Admin1
        if ($admin) {
            \\auth()->login($admin);
        }
    }
    return \\view(\'messaging.integration\');
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000d2e0000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::xbU8ygw7cGcpWcJm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::oz2er1gS4mYyQ4s9' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'test-broadcast',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:906:"function () {
    // Create a test user session for development
    if (!\\auth()->check()) {
        $admin = \\App\\Models\\Admin::find(1); // Super1 Admin1
        if ($admin) {
            \\auth()->login($admin);
        }
    }
    
    // Send a test broadcast
    \\broadcast(new \\App\\Events\\MessageSent([
        \'id\' => 999,
        \'subject\' => \'Test Broadcast Message\',
        \'message\' => \'This is a test broadcast message to verify real-time functionality.\',
        \'sender\' => \'System\',
        \'recipient\' => \\auth()->user()->first_name . \' \' . \\auth()->user()->last_name,
        \'message_type\' => \'normal\',
        \'sent_at\' => \\now()->toISOString(),
        \'is_read\' => false
    ], \\auth()->id()));
    
    return \\response()->json([
        \'success\' => true,
        \'message\' => \'Test broadcast sent successfully\',
        \'user_id\' => \\auth()->id()
    ]);
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000d340000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::oz2er1gS4mYyQ4s9',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YpeIb2zQRXslJDvP' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'test-delete-message/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:1230:"function ($id) {
    // Create a test user session for development
    if (!\\auth()->check()) {
        $admin = \\App\\Models\\Admin::find(1); // Super1 Admin1
        if ($admin) {
            \\auth()->login($admin);
        }
    }
    
    try {
        // Delete the message
        $deleted = \\DB::table(\'messages\')
            ->where(\'id\', $id)
            ->where(\'recipient_id\', \\auth()->id()) // Only allow deleting messages received by the current user
            ->delete();
        
        if ($deleted) {
            // Broadcast the deletion
            \\broadcast(new \\App\\Events\\MessageDeleted($id, \\auth()->id()));
            
            return \\response()->json([
                \'success\' => true,
                \'message\' => "Message {$id} deleted successfully"
            ]);
        } else {
            return \\response()->json([
                \'success\' => false,
                \'message\' => \'Message not found or not authorized to delete\'
            ], 404);
        }
    } catch (\\Exception $e) {
        return \\response()->json([
            \'success\' => false,
            \'message\' => \'Error deleting message: \' . $e->getMessage()
        ], 500);
    }
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000d360000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::YpeIb2zQRXslJDvP',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::IafOPAGMwjKZTjA3' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'test-create-message',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:2271:"function () {
    // Create a test user session for development
    if (!\\auth()->check()) {
        $admin = \\App\\Models\\Admin::find(1); // Super1 Admin1
        if ($admin) {
            \\auth()->login($admin);
        }
    }
    
    try {
        // Create a new test message
        $messageId = \\DB::table(\'messages\')->insertGetId([
            \'subject\' => \'Test Message \' . \\time(),
            \'message\' => \'This is a test message created at \' . \\now()->toDateTimeString(),
            \'sender\' => \'System Test\',
            \'recipient\' => \\auth()->user()->first_name . \' \' . \\auth()->user()->last_name,
            \'sender_id\' => 36464, // Vipul Kumar
            \'recipient_id\' => \\auth()->id(),
            \'sent_at\' => \\now(),
            \'is_read\' => false,
            \'message_type\' => \'normal\',
            \'client_matter_id\' => 9,
            \'client_matter_stage_id\' => 1,
            \'created_at\' => \\now(),
            \'updated_at\' => \\now()
        ]);
        
        // Broadcast the new message
        \\broadcast(new \\App\\Events\\MessageSent([
            \'id\' => $messageId,
            \'subject\' => \'Test Message \' . \\time(),
            \'message\' => \'This is a test message created at \' . \\now()->toDateTimeString(),
            \'sender\' => \'System Test\',
            \'recipient\' => \\auth()->user()->first_name . \' \' . \\auth()->user()->last_name,
            \'message_type\' => \'normal\',
            \'sent_at\' => \\now()->toISOString(),
            \'is_read\' => false
        ], \\auth()->id()));
        
        // Broadcast unread count update
        $unreadCount = \\DB::table(\'messages\')
            ->where(\'recipient_id\', \\auth()->id())
            ->where(\'is_read\', false)
            ->count();
        \\broadcast(new \\App\\Events\\UnreadCountUpdated(\\auth()->id(), $unreadCount));
        
        return \\response()->json([
            \'success\' => true,
            \'message\' => "Test message {$messageId} created successfully",
            \'unread_count\' => $unreadCount
        ]);
    } catch (\\Exception $e) {
        return \\response()->json([
            \'success\' => false,
            \'message\' => \'Error creating message: \' . $e->getMessage()
        ], 500);
    }
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000d380000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::IafOPAGMwjKZTjA3',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::95ijkbOwylkrz3di' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'test-mark-read/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:2633:"function ($id) {
    // Create a test user session for development
    if (!\\auth()->check()) {
        $admin = \\App\\Models\\Admin::find(1); // Super1 Admin1
        if ($admin) {
            \\auth()->login($admin);
        }
    }
    
    try {
        // Get the message
        $message = \\DB::table(\'messages\')
            ->where(\'id\', $id)
            ->where(\'recipient_id\', \\auth()->id())
            ->first();
        
        if (!$message) {
            return \\response()->json([
                \'success\' => false,
                \'message\' => \'Message not found\'
            ], 404);
        }
        
        if (!$message->is_read) {
            // Mark as read
            \\DB::table(\'messages\')
                ->where(\'id\', $id)
                ->update([
                    \'is_read\' => true,
                    \'read_at\' => \\now(),
                    \'updated_at\' => \\now()
                ]);
            
            // Get updated message
            $updatedMessage = \\DB::table(\'messages\')->where(\'id\', $id)->first();
            
            // Broadcast the update
            \\broadcast(new \\App\\Events\\MessageUpdated([
                \'id\' => $updatedMessage->id,
                \'subject\' => $updatedMessage->subject,
                \'message\' => $updatedMessage->message,
                \'sender\' => $updatedMessage->sender,
                \'recipient\' => $updatedMessage->recipient,
                \'is_read\' => true,
                \'read_at\' => $updatedMessage->read_at,
                \'message_type\' => $updatedMessage->message_type,
                \'sent_at\' => $updatedMessage->sent_at
            ], \\auth()->id()));
            
            // Broadcast unread count update
            $unreadCount = \\DB::table(\'messages\')
                ->where(\'recipient_id\', \\auth()->id())
                ->where(\'is_read\', false)
                ->count();
            \\broadcast(new \\App\\Events\\UnreadCountUpdated(\\auth()->id(), $unreadCount));
            
            return \\response()->json([
                \'success\' => true,
                \'message\' => "Message {$id} marked as read",
                \'unread_count\' => $unreadCount
            ]);
        } else {
            return \\response()->json([
                \'success\' => false,
                \'message\' => \'Message is already read\'
            ]);
        }
    } catch (\\Exception $e) {
        return \\response()->json([
            \'success\' => false,
            \'message\' => \'Error updating message: \' . $e->getMessage()
        ], 500);
    }
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000d3a0000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::95ijkbOwylkrz3di',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::3klaDjDE75DENAwM' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'POST',
        2 => 'HEAD',
      ),
      'uri' => 'broadcasting/auth',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => '\\Illuminate\\Broadcasting\\BroadcastController@authenticate',
        'controller' => '\\Illuminate\\Broadcasting\\BroadcastController@authenticate',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'excluded_middleware' => 
        array (
          0 => 'Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken',
        ),
        'as' => 'generated::3klaDjDE75DENAwM',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
  ),
)
);
