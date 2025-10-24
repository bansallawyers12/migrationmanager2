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
            '_route' => 'generated::oRFA6Ihlica021EJ',
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
            '_route' => 'generated::jhOkWvExQnXjrBNK',
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
            '_route' => 'generated::vHwPGGwuudsBQzKK',
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
            '_route' => 'generated::uGR7FvjVkCk1Sivp',
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
            '_route' => 'generated::JK8xguebMWLLAgYn',
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
            '_route' => 'generated::cE9l3FEMoVCtco2a',
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
            '_route' => 'generated::INiLcflYVuRT7ilf',
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
            '_route' => 'generated::9xKMBsMr2gYiwWuj',
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
            '_route' => 'generated::la2s0vfTGXPHt9oC',
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
            '_route' => 'generated::tjw2SSTjdokGKpP5',
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
            '_route' => 'generated::WarRV45IXTIvPq2i',
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
            '_route' => 'generated::eWgn8nDa4oYL9kcA',
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
            '_route' => 'generated::GlZMzYhjeqNdhEqN',
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
            '_route' => 'generated::AMScgMPKkO6WWgZL',
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
            '_route' => 'generated::MiYHiSOvbvkBeNFF',
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
            '_route' => 'generated::VxHhbz10DTK1LGZL',
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
            '_route' => 'generated::PTwvAeK4hsbWlrHH',
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
            '_route' => 'generated::4VVDxZf4Ev3tcbAu',
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
            '_route' => 'generated::VYqOUFdoN5da5pq6',
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
            '_route' => 'generated::8TFPDuNWpt9IwabP',
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
            '_route' => 'generated::PKRahvHiTYKCBAS5',
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
            '_route' => 'generated::4M3lElwdo9rImFHw',
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
            '_route' => 'generated::La9D12XHE6JvjTRJ',
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
            '_route' => 'generated::ii1SvZFMug2aNFNR',
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
            '_route' => 'generated::rUnfOCGZjmaoa1RR',
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
            '_route' => 'generated::MJ16TYg5eXMx4El9',
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
            '_route' => 'generated::gI4akCYxDeZqXi0s',
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
            '_route' => 'generated::QHLDu2lUE52D3s2e',
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
            '_route' => 'generated::H244HJMkiCejGMXa',
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
            '_route' => 'generated::zjqVk7xzrG4nhRtP',
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
            '_route' => 'generated::Jb9mDe4cd6S0tPuF',
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
            '_route' => 'generated::LHKzIYV1BuIhOtZO',
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
      '/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'crm.login',
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
            '_route' => 'crm.login.post',
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
      '/logout' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'crm.logout',
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
        1 => 
        array (
          0 => 
          array (
            '_route' => 'crm.logout.get',
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
      '/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard',
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
      '/dashboard/column-preferences' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.column-preferences',
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
      '/dashboard/update-stage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.update-stage',
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
      '/dashboard/extend-deadline' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.extend-deadline',
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
      '/dashboard/update-task-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.update-task-completed',
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
      '/dashboard/fetch-notifications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.fetch-notifications',
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
      '/dashboard/fetch-office-visit-notifications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.fetch-office-visit-notifications',
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
      '/dashboard/mark-notification-seen' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.mark-notification-seen',
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
      '/dashboard/fetch-visa-expiry-messages' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.fetch-visa-expiry-messages',
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
      '/dashboard/fetch-in-person-waiting-count' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.fetch-in-person-waiting-count',
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
      '/dashboard/fetch-total-activity-count' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.fetch-total-activity-count',
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
      '/dashboard/check-checkin-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.check-checkin-status',
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
      '/dashboard/update-checkin-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.update-checkin-status',
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
      '/my_profile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'my_profile',
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
            '_route' => 'my_profile.update',
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
      '/change_password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'change_password',
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
            '_route' => 'change_password.update',
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
      '/update_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::BLby4Mz9TyTZluS9',
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
      '/approved_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::b4FvjL1NgoGGxZvt',
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
      '/process_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Mv9L9ZijOxj1GSb5',
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
      '/archive_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::bSu5sZC4X8nLnLX8',
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
      '/declined_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ERC2JTbI18UIk0B5',
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
      '/delete_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::plURPmsoQrvPGGoC',
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
      '/move_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::fbb7D7q2sUxP0BeI',
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
      '/appointments-education' => 
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
      '/appointments-jrp' => 
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
      '/appointments-tourist' => 
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
      '/appointments-others' => 
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
      '/add_ckeditior_image' => 
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
      '/get_chapters' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'get_chapters',
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
      '/get_states' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::jMsvHmtp7rxNkQ2Z',
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
      '/settings/taxes/returnsetting' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'returnsetting',
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
      '/settings/taxes/savereturnsetting' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'savereturnsetting',
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
      '/getsubcategories' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8Rl89JtGMWOTyZki',
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
      '/getassigneeajax' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::DamrSnLrQOeqBYEd',
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
      '/getpartnerajax' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::W6EWCJAfUJXoGlr3',
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
      '/checkclientexist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::WGZZVuzhsN0U6nSD',
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
      '/leads' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.index',
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
      '/leads/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.create',
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
      '/leads/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.store',
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
      '/leads/followups/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.followups.dashboard',
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
      '/leads/analytics/trends' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.analytics.trends',
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
      '/leads/analytics/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.analytics.export',
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
      '/leads/analytics/compare-agents' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.analytics.compare',
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
      '/get-notedetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'get-notedetail',
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
      '/email_templates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'email.index',
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
      '/email_templates/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'email.create',
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
      '/email_templates/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'email.store',
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
      '/edit_email_template' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'edit_email_template.update',
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
      '/api-key' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'api',
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
            '_route' => 'api.update',
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
      '/clients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.index',
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
      '/clientsmatterslist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.clientsmatterslist',
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
      '/clientsemaillist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.clientsemaillist',
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
      '/clients/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.store',
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
      '/clients/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.update',
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
      '/clients/save-section' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveSection',
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
      '/clients/phone/send-otp' => 
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
      '/clients/phone/verify-otp' => 
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
      '/clients/phone/resend-otp' => 
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
      '/clients/email/send-verification' => 
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
      '/clients/email/resend-verification' => 
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
      '/clients/followup/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::XnL8ROtnFsWEWDaX',
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
      '/clients/followup/retagfollowup' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::a27JLqiL8leAZRqN',
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
      '/clients/removetag' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::fsmrZBO1Mp8GmZIv',
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
      '/clients/get-recipients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getrecipients',
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
      '/clients/get-onlyclientrecipients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getonlyclientrecipients',
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
      '/clients/get-allclients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getallclients',
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
      '/clients/change_assignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::QCMsVxY5JdnIB55r',
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
      '/get-templates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.gettemplates',
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
      '/sendmail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.sendmail',
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
      '/upload-mail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::TJuWNlzeoyUX0CNc',
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
      '/upload-fetch-mail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::hkpKUHjMjfGhFvaD',
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
      '/upload-sent-fetch-mail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9FeEDTI1m8qfhwMz',
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
      '/reassiginboxemail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.reassiginboxemail',
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
      '/reassigsentemail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.reassigsentemail',
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
      '/listAllMattersWRTSelClient' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.listAllMattersWRTSelClient',
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
      '/updatemailreadbit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.updatemailreadbit',
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
      '/clients/filter-emails' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.filter.emails',
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
      '/clients/filter-sentemails' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.filter.sentmails',
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
      '/mail/enhance' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'mail.enhance',
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
      '/create-note' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.createnote',
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
      '/update-note-datetime' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.updateNoteDatetime',
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
      '/getnotedetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getnotedetail',
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
      '/deletenote' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.deletenote',
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
      '/viewnotedetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Txt5jDgbE7mTgKIc',
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
      '/viewapplicationnote' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::npTO20OEdE6AaKim',
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
      '/saveprevvisa' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::zJKjMQhIGTghkEwX',
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
      '/saveonlineprimaryform' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::99XbiHc6VL6zGVmP',
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
      '/saveonlinesecform' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::CnsWhYgejf7u5Ge4',
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
      '/saveonlinechildform' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YYUbZAVRD8zKBtA2',
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
      '/get-notes' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getnotes',
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
      '/pinnote' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::1aTShGeNQ8Idjpos',
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
      '/convert-activity-to-note' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.convertActivityToNote',
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
      '/archived' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.archived',
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
      '/change-client-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.updateclientstatus',
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
      '/get-activities' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.activities',
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
      '/deletecostagreement' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.deletecostagreement',
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
      '/deleteactivitylog' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.deleteactivitylog',
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
      '/not-picked-call' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.notpickedcall',
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
      '/pinactivitylog' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::FJ9zNnX5oF9rxRMf',
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
      '/interested-service' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::sHszJAi8jVGnQK3r',
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
      '/edit-interested-service' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::wD9mqONFam1jSc6n',
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
      '/get-services' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9IoZT2Esks1irt4T',
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
      '/servicesavefee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::FMmXJUISslKrQ5R6',
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
      '/getintrestedservice' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5YbjZqUhGiGhaqwB',
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
      '/getintrestedserviceedit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::mt1eEgTIp7nxulpT',
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
      '/get-application-lists' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getapplicationlists',
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
      '/saveapplication' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveapplication',
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
      '/convertapplication' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.convertapplication',
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
      '/deleteservices' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.deleteservices',
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
      '/savetoapplication' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Ugi4a7GrpQNc8ymA',
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
      '/client/createservicetaken' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9LE1mvIHWx234U2h',
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
      '/client/removeservicetaken' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ogVrvryALPnrLC3S',
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
      '/client/getservicetaken' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::UemaRnoaYWPXgCeR',
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
      '/documents/add-edu-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.addedudocchecklist',
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
      '/documents/upload-edu-document' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.uploadedudocument',
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
      '/documents/add-visa-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.addvisadocchecklist',
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
      '/documents/upload-visa-document' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.uploadvisadocument',
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
      '/documents/rename' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.renamedoc',
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
      '/documents/delete' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.deletedocs',
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
      '/documents/get-visa-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.getvisachecklist',
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
      '/documents/not-used' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.notuseddoc',
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
      '/documents/rename-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.renamechecklistdoc',
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
      '/documents/back-to-doc' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.backtodoc',
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
      '/documents/download' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.download',
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
      '/documents/add-personal-category' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.addPersonalDocCategory',
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
      '/documents/update-personal-category' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.updatePersonalDocCategory',
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
      '/documents/add-visa-category' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.addVisaDocCategory',
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
      '/documents/update-visa-category' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.updateVisaDocCategory',
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
      '/clients/saveaccountreport' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveaccountreport.update',
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
      '/clients/saveinvoicereport' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveinvoicereport.update',
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
      '/clients/saveadjustinvoicereport' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveadjustinvoicereport.update',
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
      '/clients/saveofficereport' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveofficereport.update',
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
      '/clients/savejournalreport' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.savejournalreport.update',
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
      '/clients/isAnyInvoiceNoExistInDB' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.isAnyInvoiceNoExistInDB',
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
      '/clients/listOfInvoice' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.listOfInvoice',
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
      '/clients/getTopReceiptValInDB' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getTopReceiptValInDB',
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
      '/clients/getInfoByReceiptId' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getInfoByReceiptId',
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
      '/clients/getTopInvoiceNoFromDB' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getTopInvoiceNoFromDB',
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
      '/clients/clientLedgerBalanceAmount' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.clientLedgerBalanceAmount',
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
      '/clients/invoicelist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.invoicelist',
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
      '/void_invoice' => 
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
      '/clients/clientreceiptlist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.clientreceiptlist',
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
      '/clients/officereceiptlist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.officereceiptlist',
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
      '/clients/journalreceiptlist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.journalreceiptlist',
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
      '/validate_receipt' => 
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
      '/delete_receipt' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::DBerHGDcNV5f08oe',
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
      '/update-client-funds-ledger' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.update-client-funds-ledger',
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
      '/clients/invoiceamount' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.invoiceamount',
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
      '/clients/update-address' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.updateAddress',
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
      '/clients/search-address-full' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.searchAddressFull',
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
      '/clients/get-place-details' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getPlaceDetails',
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
      '/address_auto_populate' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::GeZU6wg2mxDuTJye',
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
      '/clients/fetchClientContactNo' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::QsfeWpaMUlGgWe0V',
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
      '/clients/clientdetailsinfo' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.clientdetailsinfo.update',
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
      '/get-visa-types' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'getVisaTypes',
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
      '/get-countries' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'getCountries',
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
      '/updateOccupation' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.updateOccupation',
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
      '/clients/search-partner' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.searchPartner',
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
      '/clients/search-partner-test' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.searchPartnerTest',
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
      '/clients/test-bidirectional' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.testBidirectional',
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
      '/clients/save-relationship' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveRelationship',
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
      '/clients/generateagreement' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.generateagreement',
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
      '/clients/getMigrationAgentDetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getMigrationAgentDetail',
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
      '/clients/getVisaAggreementMigrationAgentDetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getVisaAggreementMigrationAgentDetail',
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
      '/clients/getCostAssignmentMigrationAgentDetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getCostAssignmentMigrationAgentDetail',
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
      '/clients/savecostassignment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.savecostassignment',
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
      '/clients/check-cost-assignment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::e5A4ZpgwWrtpaxD1',
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
      '/clients/savecostassignmentlead' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.savecostassignmentlead',
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
      '/clients/getCostAssignmentMigrationAgentDetailLead' => 
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
      '/forms' => 
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
      '/get-matter-templates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getmattertemplates',
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
      '/clients/fetchClientMatterAssignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::moSUG3T3V6g2gKuk',
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
      '/clients/updateClientMatterAssignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::pbSpncNDcSZOCpmK',
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
      '/upload-checklists' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'upload_checklists.index',
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
      '/upload-checklists/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'upload_checklistsupload',
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
      '/clients/personalfollowup/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::IYvM0B4S8WPDk7d5',
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
      '/clients/updatefollowup/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::coMRnYEwXN1GvRbb',
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
      '/clients/reassignfollowup/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::GGS6tLXXkyg3WTK0',
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
      '/clients/update-session-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.updatesessioncompleted',
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
      '/clients/getAllUser' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getAllUser',
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
      '/clients/toggle-client-portal' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.toggleClientPortal',
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
      '/anzsco/search' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'anzsco.search',
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
      '/check-email' => 
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
      '/check.phone' => 
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
      '/save_tag' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::42T8rX2P2rBcOvYz',
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
      '/save-references' => 
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
      '/check-star-client' => 
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
      '/merge_records' => 
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
      '/send-webhook' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'send-webhook',
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
      '/fetch-visa_expiry_messages' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::abevjveklLkzlX7d',
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
      '/applications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'applications.index',
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
      '/applications/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'applications.create',
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
      '/applications-import' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'applications.import',
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
      '/getapplicationdetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5ILg5C0x7I21CF7Z',
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
      '/load-application-insert-update-data' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::TilzQ08M9E6lFXoT',
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
      '/updatestage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::tBhSRcoNFikujDGy',
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
      '/completestage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::McsRmlwfkezR53Ez',
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
      '/updatebackstage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::B7bsTcLNaiG9GW5m',
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
      '/get-applications-logs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::XQ2f6qXKtadNAZIw',
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
      '/get-applications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::pM3n0wGxquUjJ8W2',
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
      '/discontinue_application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::vfv4ci04yBJPAykb',
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
      '/revert_application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::BeAyymbKS96HhQ2l',
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
      '/create-app-note' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::tvn3QaErXJvZfKRn',
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
      '/getapplicationnotes' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::UL1KgsZUAlQSqlv2',
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
      '/application-sendmail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::VlaADIrgNJ9iwsbc',
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
      '/application/updateintake' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::kmvBzMFbXmLqSaZi',
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
      '/application/updatedates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::HIRnHRHVZWqODFKU',
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
      '/application/updateexpectwin' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::jbzWzEcY8pymrd98',
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
      '/application/getapplicationbycid' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::OuR235WSgnIEL7u9',
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
      '/application/spagent_application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::SY4xKjaG8puugULE',
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
      '/application/sbagent_application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::GeNBCUenYwjYfK2h',
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
      '/application/application_ownership' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::wW2xYvXfXXnGFIl1',
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
      '/superagent' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::m5RmPWQC9In1iJDY',
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
      '/subagent' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::EIpA4iyusrRs4IBQ',
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
      '/applicationsavefee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::1BqwlKu79oU3BpEg',
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
      '/add-checklists' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::apvXtGD1hvJZsvTF',
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
      '/application/checklistupload' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::wwtbUA55UOHn1EPC',
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
      '/deleteapplicationdocs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::RH6DVzgYsUBEEd0r',
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
      '/application/publishdoc' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ronlJzovTApXOGaa',
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
      '/migration' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'migration.index',
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
      '/office-visits' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'officevisits.index',
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
      '/office-visits/waiting' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'officevisits.waiting',
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
      '/office-visits/attending' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'officevisits.attending',
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
      '/office-visits/completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'officevisits.completed',
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
      '/office-visits/archived' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'officevisits.archived',
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
      '/office-visits/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'officevisits.create',
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
      '/checkin' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::nu5ReDEjjF1712X3',
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
      '/get-checkin-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::vr5ADKLsAzMHYLa3',
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
      '/update_visit_purpose' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Re5Dem3fFd3po16S',
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
      '/update_visit_comment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::E6XW2IhNoFEUXzE7',
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
      '/attend_session' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::OaXOo5Cifh6fS9ue',
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
      '/complete_session' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::CxR0SycsexJMOOKO',
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
      '/office-visits/change_assignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::bP51QVyRogAAeywy',
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
      '/appointments' => 
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
      '/appointments/create' => 
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
      '/appointments-adelaide' => 
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
      '/deleteappointment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::jRCV0YywhUw0zzjf',
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
      '/add-appointment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::x5I1Xwti1hxjrqcs',
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
      '/add-appointment-book' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::SWSwCLNpOSCMelXU',
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
      '/editappointment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::1pPIS80rCVSwBGQV',
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
      '/updatefollowupschedule' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ThLT4fTLIT4pVkgb',
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
      '/update_appointment_status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::48SGU6rBnF3fkR8d',
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
      '/update_appointment_priority' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::kpDPWRlazljRJ1aC',
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
      '/update_apppointment_comment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::3mmVro7rm1V9IWYX',
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
      '/update_apppointment_description' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::FcbH3iNsmePLh2Z0',
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
      '/get-appointments' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::jnIWXgrBxlKi9U3M',
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
      '/getAppointmentdetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Uz0FuoFIPUt3PTNm',
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
      '/get-assigne-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::x1hohSJOtQTk7eZ0',
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
      '/change_assignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::HTqoJ0O5Yw7dBpgs',
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
      '/getdatetimebackend' => 
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
      '/getdisableddatetime' => 
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
      '/booking/appointments' => 
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
      '/booking/appointments/bulk-update-status' => 
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
      '/booking/appointments/export' => 
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
      '/booking/sync/dashboard' => 
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
      '/booking/sync/stats' => 
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
      '/booking/sync/manual' => 
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
      '/booking/api/appointments' => 
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
      '/audit-logs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'auditlogs.index',
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
      '/fetch-notification' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::w1vOAhbKElypUhsZ',
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
      '/fetch-messages' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::1LQ0sdBKmJXavkQG',
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
      '/fetch-office-visit-notifications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::XOGnHcuQ3FJXnW1O',
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
      '/mark-notification-seen' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::1z26eBfW3JDP50K9',
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
      '/check-checkin-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9VXMgaiYCespmPgj',
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
      '/update-checkin-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::UwBS9tZuZekW90ce',
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
      '/all-notifications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::eJI0oXSDmqzjmN92',
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
      '/fetch-InPersonWaitingCount' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::lbm3JxLkHJWpmTDn',
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
      '/fetch-TotalActivityCount' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Tim8bmFkaPJG5GZi',
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
      '/assignee' => 
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
      '/assignee/create' => 
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
      '/assignee-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::0Eryx64UdCmIZHvH',
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
      '/update-task-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::7TBZnAj523c1CWXM',
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
      '/update-task-not-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8nftBvTfNs6fkOjE',
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
      '/assigned_by_me' => 
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
      '/assigned_to_me' => 
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
      '/action_completed' => 
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
      '/is_email_unique' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::87pqsLlVqDtzHUZX',
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
      '/is_contactno_unique' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5PknYMVEGeheO4Je',
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
      '/extenddeadlinedate' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::C6FtbkJ7HFfhNj8B',
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
      '/update-stage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::pM0sGFwy0ePa17C5',
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
      '/get_assignee_list' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9XFhqr95Y1dQiroI',
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
      '/update-task' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ldCOvQkNdiCzWmpt',
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
      '/action/counts' => 
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
      '/action' => 
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
      '/action/list' => 
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
      '/test-signature' => 
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
      '/doc-to-pdf' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'doc-to-pdf.form',
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
      '/doc-to-pdf/convert' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'doc-to-pdf.convert',
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
      '/doc-to-pdf/test' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'doc-to-pdf.test',
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
      '/doc-to-pdf/test-python' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'doc-to-pdf.test-python',
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
      '/doc-to-pdf/debug' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'doc-to-pdf.debug',
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
      '/signatures' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.index',
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
            '_route' => 'signatures.store',
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
      '/signatures/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.create',
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
      '/signatures/suggest-association' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.suggest-association',
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
      '/signatures/preview-email' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.preview-email',
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
      '/signatures/bulk-archive' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.bulk-archive',
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
      '/signatures/bulk-void' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.bulk-void',
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
      '/signatures/bulk-resend' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.bulk-resend',
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
      '/documents/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.create',
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
            '_route' => 'generated::CD53pecQS9juZBCV',
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
            '_route' => 'generated::aSFOl8rLmdhY6Uz7',
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
            '_route' => 'generated::LYYGc10NNSdlCCpu',
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
            '_route' => 'generated::LNrCGvbfTrHtlnnM',
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
      0 => '{^(?|/oauth/(?|tokens/([^/]++)(*:32)|clients/([^/]++)(?|(*:58))|personal\\-access\\-tokens/([^/]++)(*:99))|/c(?|aptcha(?|/api(?:/([^/]++))?(*:139)|(?:/([^/]++))?(*:161))|lients/(?|e(?|dit/([^/]++)(*:196)|mail/status/([^/]++)(*:224))|p(?|artner\\-eoi\\-data/([^/]++)(*:263)|hone/status/([^/]++)(*:291))|changetype/([^/]++)/([^/]++)(*:328)|detail/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?(*:379)|([^/]++)/eoi\\-roi(?|(*:407)|/(?|calculate\\-points(*:436)|([^/]++)(?|(*:455)|/reveal\\-password(*:480)))|(*:490))|s(?|ave(?|a(?|ccountreport/([^/]++)(*:534)|djustinvoicereport/([^/]++)(*:569))|invoicereport/([^/]++)(*:600)|officereport/([^/]++)(*:629)|journalreport/([^/]++)(*:659))|endToHubdoc/([^/]++)(*:688))|gen(?|Invoice/([^/]++)(*:719)|ClientFundLedgerInvoice/([^/]++)(*:759)|officereceiptInvoice/([^/]++)(*:796))|c(?|heckHubdocStatus/([^/]++)(*:834)|lientdetailsinfo/([^/]++)(*:867))|printPreview/([^/]++)(*:897)|([^/]++)/(?|upload\\-agreement(*:934)|matters(*:949))))|/a(?|p(?|i/(?|workflow/stages/([^/]++)(*:998)|messages/([^/]++)(?|/read(*:1031)|(*:1040)))|p(?|lication(?|s/detail/([^/]++)(*:1083)|/export/pdf/([^/]++)(*:1112))|ointments/([^/]++)(?|(*:1143)|/edit(*:1157)|(*:1166))))|dminconsole/(?|features/(?|matter(?|/(?|edit/([^/]++)(*:1231)|([^/]++)(*:1248))|\\-(?|email\\-template/(?|edit/([^/]++)(*:1295)|([^/]++)(*:1312))|other\\-email\\-template/(?|edit/([^/]++)(*:1361)|([^/]++)(*:1378))))|tags/(?|edit/([^/]++)(*:1411)|([^/]++)(*:1428))|workflow/(?|edit/([^/]++)(*:1463)|([^/]++)(*:1480)|deactivate\\-workflow/([^/]++)(*:1518)|activate\\-workflow/([^/]++)(*:1554))|emails/(?|edit/([^/]++)(*:1587)|([^/]++)(*:1604))|crm\\-email\\-template/(?|edit/([^/]++)(*:1651)|([^/]++)(*:1668))|personal\\-document\\-type/(?|edit/([^/]++)(*:1719)|([^/]++)(*:1736)|checkcreatefolder(*:1762))|visa\\-document\\-type/(?|edit/([^/]++)(*:1809)|([^/]++)(*:1826)|checkcreatefolder(*:1852))|document\\-checklist/(?|edit/([^/]++)(*:1898)|([^/]++)(*:1915))|sms/(?|history/([^/]++)(*:1948)|status/([^/]++)(*:1972)|templates/([^/]++)(?|(*:2002)|/edit(*:2016)|(*:2025))))|system/(?|users/(?|edit/([^/]++)(*:2069)|view/([^/]++)(*:2091)|([^/]++)(*:2108)|s(?|avezone(*:2128)|toreclient(*:2147))|active(*:2163)|in(?|active(*:2183)|vited(*:2197))|c(?|lient(?|list(*:2223)|/([^/]++)(*:2241))|reateclient(*:2262))|editclient/([^/]++)(*:2291))|roles/(?|edit/([^/]++)(*:2323)|([^/]++)(*:2340))|teams/(?|edit/([^/]++)(*:2372)|([^/]++)(*:2389))|offices/(?|edit/([^/]++)(*:2423)|view/(?|([^/]++)(*:2448)|client/([^/]++)(*:2472))|([^/]++)(*:2490)))|database/anzsco/(?|edit/([^/]++)(*:2533)|([^/]++)(*:2550)|import(?|(*:2568))))|ssignee/([^/]++)(?|(*:2599)|/edit(*:2613)|(*:2622)))|/leads/(?|detail/([^/]++)(*:2658)|history/([^/]++)(*:2683)|([^/]++)(?|/edit(*:2708)|(*:2717))|assign(?|(*:2736)|able\\-users(*:2756))|bulk\\-(?|assign(*:2781)|convert(*:2797))|conver(?|t(?|(*:2820)|\\-single(*:2837))|sion\\-stats(*:2858))|followups(?|(*:2880)|/([^/]++)/(?|c(?|omplete(*:2913)|ancel(*:2927))|reschedule(*:2947)))|([^/]++)/followups(*:2976)|analytics(*:2994)|notes/delete/([^/]++)(*:3024)|pin/([^/]++)(*:3045)|updateOccupation(*:3070))|/edit_email_template/([^/]++)(*:3109)|/d(?|ocument(?|/download/pdf/([^/]++)(*:3155)|s(?|/([^/]++)/(?|sign(*:3185)|page/([^/]++)(*:3207))|(?:/([0-9]+))?(*:3231)|/(?|([^/]++)/download\\-signed(?|(*:3272)|\\-and\\-thankyou(*:3296))|thankyou(?:/([^/]++))?(*:3328)|([^/]++)(?|/(?|s(?|end\\-(?|reminder(*:3372)|signing\\-link(*:3394))|ign(*:3407))|edit(*:3421))|(*:3431)))|(*:3442)))|e(?|stroy_(?|by_me/([^/]++)(*:3480)|to_me/([^/]++)(*:3503)|activity/([^/]++)(*:3529)|complete_activity/([^/]++)(*:3564))|bug\\-pdf\\-page/([^/]++)/([^/]++)(*:3606)))|/forms/([^/]++)(?|(*:3635)|/p(?|review(*:3655)|df(*:3666)))|/get\\-client\\-matters/([^/]++)(*:3707)|/up(?|load\\-checklists/matter/([^/]++)(*:3754)|dateappointmentstatus/([^/]++)/([^/]++)(*:3802))|/verify\\-email/([^/]++)(*:3835)|/booking/(?|appointments/(?|([0-9]+)(*:3880)|([0-9]+)/json(*:3902)|([0-9]+)/update\\-status(*:3934)|([0-9]+)/update\\-consultant(*:3970)|([0-9]+)/add\\-note(*:3997)|([0-9]+)/update\\-followup(*:4031)|([0-9]+)/send\\-reminder(*:4063))|calendar/(paid|jrp|education|tourist|adelaide)(*:4119))|/sign(?|atures/(?|([^/]++)(?|(*:4158)|/(?|reminder(*:4179)|send(*:4192)|copy\\-link(*:4211)|associate(*:4229)))|api/client\\-matters/([^/]++)(*:4268)|([^/]++)/detach(*:4292))|/([^/]++)/([^/]++)(*:4320))|/test\\-(?|delete\\-message/([^/]++)(*:4364)|mark\\-read/([^/]++)(*:4392)))/?$}sDu',
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
      139 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::MKFyLswccGKsVWje',
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
      161 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::03uYvkzNjB4mVEdC',
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
      196 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.edit',
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
      224 => 
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
      263 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.partnerEoiData',
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
      291 => 
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
      328 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::HsmMXEk4gV8OJYkF',
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
      379 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.detail',
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
      407 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.eoi-roi.index',
          ),
          1 => 
          array (
            0 => 'client',
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
      436 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.eoi-roi.calculatePoints',
          ),
          1 => 
          array (
            0 => 'client',
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
      455 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.eoi-roi.show',
          ),
          1 => 
          array (
            0 => 'client',
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
            '_route' => 'clients.eoi-roi.destroy',
          ),
          1 => 
          array (
            0 => 'client',
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
      480 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.eoi-roi.revealPassword',
          ),
          1 => 
          array (
            0 => 'client',
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
      490 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.eoi-roi.upsert',
          ),
          1 => 
          array (
            0 => 'client',
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
      534 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveaccountreport',
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
      569 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveadjustinvoicereport',
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
      600 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveinvoicereport',
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
      629 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveofficereport',
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
      659 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.savejournalreport',
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
      688 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.sendToHubdoc',
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
      719 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Zi9Af5Ee4qIJIkAx',
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
      759 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::FRdsIMAunYiVsK1G',
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
      796 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::LqTBTEUjacGqvDJu',
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
      834 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.checkHubdocStatus',
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
      867 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.clientdetailsinfo',
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
      897 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::sz3HxHnWXUCAod8k',
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
      934 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.uploadAgreement',
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
      949 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.matters',
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
      998 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::GaPYLm3Sodjbg9kZ',
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
      1031 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::JG7yOVfAysxv3GYL',
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
      1040 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Qi0x4RtBLdVChh9g',
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
      1083 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'applications.detail',
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
      1112 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::TSmJwShUdIbgL7A4',
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
      1143 => 
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
      1157 => 
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
      1166 => 
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
      1231 => 
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
      1248 => 
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
      1295 => 
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
      1312 => 
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
      1361 => 
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
      1378 => 
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
      1411 => 
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
      1428 => 
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
      1463 => 
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
      1480 => 
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
      1518 => 
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
      1554 => 
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
      1587 => 
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
      1604 => 
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
      1651 => 
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
      1668 => 
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
      1719 => 
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
      1736 => 
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
      1762 => 
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
      1809 => 
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
      1826 => 
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
      1852 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.generated::EaVVwTNdTepsfIcQ',
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
      1898 => 
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
      1915 => 
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
      1948 => 
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
      1972 => 
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
      2002 => 
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
      2016 => 
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
      2025 => 
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
      2069 => 
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
      2091 => 
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
      2108 => 
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
      2128 => 
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
      2147 => 
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
      2163 => 
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
      2183 => 
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
      2197 => 
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
      2223 => 
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
      2241 => 
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
      2262 => 
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
      2291 => 
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
      2323 => 
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
      2340 => 
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
      2372 => 
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
      2389 => 
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
      2423 => 
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
      2448 => 
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
      2472 => 
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
      2490 => 
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
      2533 => 
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
      2550 => 
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
      2568 => 
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
      2599 => 
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
      2613 => 
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
      2622 => 
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
      2658 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.detail',
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
      2683 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.history',
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
      2708 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.edit',
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
      2717 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.update',
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
            '_route' => 'leads.patch',
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
      2736 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.assign',
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
      2756 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.assignable_users',
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
      2781 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.bulk_assign',
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
      2797 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.bulk_convert',
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
      2820 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.convert',
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
      2837 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.convert_single',
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
      2858 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.conversion_stats',
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
      2880 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.followups.index',
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
            '_route' => 'leads.followups.store',
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
      2913 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.followups.complete',
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
      2927 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.followups.cancel',
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
      2947 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.followups.reschedule',
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
      2976 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.followups.get',
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
      2994 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.analytics.index',
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
      3024 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.notes.delete',
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
      3045 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.pin',
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
      3070 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.updateOccupation',
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
      3109 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'edit_email_template',
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
      3155 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::nak3hJrVxdWjjqsF',
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
      3185 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.submitSignatures',
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
      3207 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.page',
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
      3231 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.index',
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
      3272 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.download.signed',
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
      3296 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.download_and_thankyou',
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
      3328 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.thankyou',
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
      3372 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.sendReminder',
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
      3394 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.sendSigningLink',
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
      3407 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.showSignForm',
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
      3421 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.edit',
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
      3431 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.update',
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
      3442 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.store',
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
      3480 => 
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
      3503 => 
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
      3529 => 
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
      3564 => 
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
      3606 => 
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
      3635 => 
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
      3655 => 
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
      3666 => 
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
      3707 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getClientMatters',
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
      3754 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'upload_checklists.matter',
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
      3802 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::QE2kwoCTK2mr5bTm',
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
      3835 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.email.verify',
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
      3880 => 
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
      3902 => 
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
      3934 => 
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
      3970 => 
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
      3997 => 
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
      4031 => 
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
      4063 => 
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
      4119 => 
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
      4158 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.show',
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
      4179 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.reminder',
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
      4192 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.send',
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
      4211 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.copy-link',
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
      4229 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.associate',
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
      4268 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.client-matters',
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
      4292 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.detach',
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
      4320 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.sign',
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
      4364 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::qCGrZvtGIqUdFAEt',
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
      4392 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::3dZ4xFV2cbmJtCVw',
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
    'generated::MKFyLswccGKsVWje' => 
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
        'as' => 'generated::MKFyLswccGKsVWje',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::03uYvkzNjB4mVEdC' => 
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
        'as' => 'generated::03uYvkzNjB4mVEdC',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
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
    'generated::oRFA6Ihlica021EJ' => 
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
        'as' => 'generated::oRFA6Ihlica021EJ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::jhOkWvExQnXjrBNK' => 
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
        'as' => 'generated::jhOkWvExQnXjrBNK',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::vHwPGGwuudsBQzKK' => 
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
        'as' => 'generated::vHwPGGwuudsBQzKK',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::uGR7FvjVkCk1Sivp' => 
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
        'as' => 'generated::uGR7FvjVkCk1Sivp',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::JK8xguebMWLLAgYn' => 
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
        'as' => 'generated::JK8xguebMWLLAgYn',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::cE9l3FEMoVCtco2a' => 
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
        'as' => 'generated::cE9l3FEMoVCtco2a',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::INiLcflYVuRT7ilf' => 
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
        'as' => 'generated::INiLcflYVuRT7ilf',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9xKMBsMr2gYiwWuj' => 
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
        'as' => 'generated::9xKMBsMr2gYiwWuj',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::la2s0vfTGXPHt9oC' => 
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
        'as' => 'generated::la2s0vfTGXPHt9oC',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::tjw2SSTjdokGKpP5' => 
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
        'as' => 'generated::tjw2SSTjdokGKpP5',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::WarRV45IXTIvPq2i' => 
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
        'as' => 'generated::WarRV45IXTIvPq2i',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::eWgn8nDa4oYL9kcA' => 
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
        'as' => 'generated::eWgn8nDa4oYL9kcA',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::GlZMzYhjeqNdhEqN' => 
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
        'as' => 'generated::GlZMzYhjeqNdhEqN',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::AMScgMPKkO6WWgZL' => 
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
        'as' => 'generated::AMScgMPKkO6WWgZL',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::MiYHiSOvbvkBeNFF' => 
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
        'as' => 'generated::MiYHiSOvbvkBeNFF',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::VxHhbz10DTK1LGZL' => 
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
        'as' => 'generated::VxHhbz10DTK1LGZL',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::PTwvAeK4hsbWlrHH' => 
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
        'as' => 'generated::PTwvAeK4hsbWlrHH',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::4VVDxZf4Ev3tcbAu' => 
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
        'as' => 'generated::4VVDxZf4Ev3tcbAu',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::VYqOUFdoN5da5pq6' => 
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
        'as' => 'generated::VYqOUFdoN5da5pq6',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8TFPDuNWpt9IwabP' => 
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
        'as' => 'generated::8TFPDuNWpt9IwabP',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::PKRahvHiTYKCBAS5' => 
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
        'as' => 'generated::PKRahvHiTYKCBAS5',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::4M3lElwdo9rImFHw' => 
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
        'as' => 'generated::4M3lElwdo9rImFHw',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::La9D12XHE6JvjTRJ' => 
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
        'as' => 'generated::La9D12XHE6JvjTRJ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::GaPYLm3Sodjbg9kZ' => 
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
        'as' => 'generated::GaPYLm3Sodjbg9kZ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ii1SvZFMug2aNFNR' => 
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
        'as' => 'generated::ii1SvZFMug2aNFNR',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::rUnfOCGZjmaoa1RR' => 
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
        'as' => 'generated::rUnfOCGZjmaoa1RR',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::MJ16TYg5eXMx4El9' => 
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
        'as' => 'generated::MJ16TYg5eXMx4El9',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::gI4akCYxDeZqXi0s' => 
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
        'as' => 'generated::gI4akCYxDeZqXi0s',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::QHLDu2lUE52D3s2e' => 
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
        'as' => 'generated::QHLDu2lUE52D3s2e',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::JG7yOVfAysxv3GYL' => 
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
        'as' => 'generated::JG7yOVfAysxv3GYL',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Qi0x4RtBLdVChh9g' => 
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
        'as' => 'generated::Qi0x4RtBLdVChh9g',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::H244HJMkiCejGMXa' => 
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
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000aec0000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::H244HJMkiCejGMXa',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::zjqVk7xzrG4nhRtP' => 
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
        'as' => 'generated::zjqVk7xzrG4nhRtP',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Jb9mDe4cd6S0tPuF' => 
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
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:58:"function() {
    return \\redirect()->route(\'crm.login\');
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000b0a0000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::Jb9mDe4cd6S0tPuF',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::LHKzIYV1BuIhOtZO' => 
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
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:270:"function() {
    \\Artisan::call(\'config:clear\');
    \\Artisan::call(\'view:clear\');
    \\Artisan::call(\'route:clear\');
    \\Artisan::call(\'route:cache\');
    return \\response()->json([
        \'success\' => true,
        \'message\' => \'Cache cleared successfully\'
    ]);
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000b0c0000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::LHKzIYV1BuIhOtZO',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
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
    'adminconsole.features.generated::EaVVwTNdTepsfIcQ' => 
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
        'as' => 'adminconsole.features.generated::EaVVwTNdTepsfIcQ',
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
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@clientsemaillist',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@clientsemaillist',
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
    'crm.login' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@showLoginForm',
        'controller' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@showLoginForm',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'crm.login',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'crm.login.post' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@login',
        'controller' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@login',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'crm.login.post',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'crm.logout' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'logout',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@logout',
        'controller' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@logout',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'crm.logout',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'crm.logout.get' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'logout',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:58:"function() {
    return \\redirect()->route(\'crm.login\');
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000b130000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'crm.logout.get',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DashboardController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\DashboardController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.column-preferences' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'dashboard/column-preferences',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DashboardController@saveColumnPreferences',
        'controller' => 'App\\Http\\Controllers\\CRM\\DashboardController@saveColumnPreferences',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.column-preferences',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.update-stage' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'dashboard/update-stage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DashboardController@updateStage',
        'controller' => 'App\\Http\\Controllers\\CRM\\DashboardController@updateStage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.update-stage',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.extend-deadline' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'dashboard/extend-deadline',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DashboardController@extendDeadlineDate',
        'controller' => 'App\\Http\\Controllers\\CRM\\DashboardController@extendDeadlineDate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.extend-deadline',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.update-task-completed' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'dashboard/update-task-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DashboardController@updateTaskCompleted',
        'controller' => 'App\\Http\\Controllers\\CRM\\DashboardController@updateTaskCompleted',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.update-task-completed',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.fetch-notifications' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dashboard/fetch-notifications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchnotification',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchnotification',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.fetch-notifications',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.fetch-office-visit-notifications' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dashboard/fetch-office-visit-notifications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchOfficeVisitNotifications',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchOfficeVisitNotifications',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.fetch-office-visit-notifications',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.mark-notification-seen' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'dashboard/mark-notification-seen',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@markNotificationSeen',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@markNotificationSeen',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.mark-notification-seen',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.fetch-visa-expiry-messages' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dashboard/fetch-visa-expiry-messages',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchvisaexpirymessages',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchvisaexpirymessages',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.fetch-visa-expiry-messages',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.fetch-in-person-waiting-count' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dashboard/fetch-in-person-waiting-count',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchInPersonWaitingCount',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchInPersonWaitingCount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.fetch-in-person-waiting-count',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.fetch-total-activity-count' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dashboard/fetch-total-activity-count',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchTotalActivityCount',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchTotalActivityCount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.fetch-total-activity-count',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.check-checkin-status' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'dashboard/check-checkin-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DashboardController@checkCheckinStatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\DashboardController@checkCheckinStatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.check-checkin-status',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.update-checkin-status' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'dashboard/update-checkin-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DashboardController@updateCheckinStatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\DashboardController@updateCheckinStatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.update-checkin-status',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'my_profile' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'my_profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@myProfile',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@myProfile',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'my_profile',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'my_profile.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'my_profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@myProfile',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@myProfile',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'my_profile.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'change_password' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'change_password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@change_password',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@change_password',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'change_password',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'change_password.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'change_password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@change_password',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@change_password',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'change_password.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::BLby4Mz9TyTZluS9' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@updateAction',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@updateAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::BLby4Mz9TyTZluS9',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::b4FvjL1NgoGGxZvt' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'approved_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@approveAction',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@approveAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::b4FvjL1NgoGGxZvt',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Mv9L9ZijOxj1GSb5' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'process_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@processAction',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@processAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::Mv9L9ZijOxj1GSb5',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::bSu5sZC4X8nLnLX8' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'archive_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@archiveAction',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@archiveAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::bSu5sZC4X8nLnLX8',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ERC2JTbI18UIk0B5' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'declined_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@declinedAction',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@declinedAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::ERC2JTbI18UIk0B5',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::plURPmsoQrvPGGoC' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'delete_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@deleteAction',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@deleteAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::plURPmsoQrvPGGoC',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::fbb7D7q2sUxP0BeI' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'move_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@moveAction',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@moveAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::fbb7D7q2sUxP0BeI',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
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
      'uri' => 'appointments-education',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsEducation',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsEducation',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'appointments-jrp',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsJrp',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsJrp',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'appointments-tourist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsTourist',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsTourist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'appointments-others',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsOthers',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsOthers',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'add_ckeditior_image',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@addCkeditiorImage',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@addCkeditiorImage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
    'get_chapters' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'get_chapters',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getChapters',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getChapters',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'get_chapters',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::jMsvHmtp7rxNkQ2Z' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'get_states',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getStates',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getStates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::jMsvHmtp7rxNkQ2Z',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'returnsetting' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'settings/taxes/returnsetting',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@returnsetting',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@returnsetting',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'returnsetting',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'savereturnsetting' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'settings/taxes/savereturnsetting',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@returnsetting',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@returnsetting',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'savereturnsetting',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8Rl89JtGMWOTyZki' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'getsubcategories',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getsubcategories',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getsubcategories',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::8Rl89JtGMWOTyZki',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::DamrSnLrQOeqBYEd' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'getassigneeajax',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getassigneeajax',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getassigneeajax',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::DamrSnLrQOeqBYEd',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::W6EWCJAfUJXoGlr3' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'getpartnerajax',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getpartnerajax',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getpartnerajax',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::W6EWCJAfUJXoGlr3',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::WGZZVuzhsN0U6nSD' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'checkclientexist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@checkclientexist',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@checkclientexist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::WGZZVuzhsN0U6nSD',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@index',
        'as' => 'leads.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
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
    'leads.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/detail/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@detail',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@detail',
        'as' => 'leads.detail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
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
    'leads.history' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/history/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@history',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@history',
        'as' => 'leads.history',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
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
    'leads.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@create',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@create',
        'as' => 'leads.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
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
    'leads.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@store',
        'as' => 'leads.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
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
    'leads.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/{id}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@edit',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@edit',
        'as' => 'leads.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
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
    'leads.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'leads/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@update',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@update',
        'as' => 'leads.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
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
    'leads.patch' => 
    array (
      'methods' => 
      array (
        0 => 'PATCH',
      ),
      'uri' => 'leads/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@update',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@update',
        'as' => 'leads.patch',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
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
    'leads.assign' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/assign',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAssignmentController@assign',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAssignmentController@assign',
        'as' => 'leads.assign',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
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
    'leads.bulk_assign' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/bulk-assign',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAssignmentController@bulkAssign',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAssignmentController@bulkAssign',
        'as' => 'leads.bulk_assign',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
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
    'leads.assignable_users' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/assignable-users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAssignmentController@getAssignableUsers',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAssignmentController@getAssignableUsers',
        'as' => 'leads.assignable_users',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
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
    'leads.convert' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/convert',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadConversionController@convertToClient',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadConversionController@convertToClient',
        'as' => 'leads.convert',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
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
    'leads.convert_single' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/convert-single',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadConversionController@convertSingleLead',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadConversionController@convertSingleLead',
        'as' => 'leads.convert_single',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
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
    'leads.bulk_convert' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/bulk-convert',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadConversionController@bulkConvertToClient',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadConversionController@bulkConvertToClient',
        'as' => 'leads.bulk_convert',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
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
    'leads.conversion_stats' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/conversion-stats',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadConversionController@getConversionStats',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadConversionController@getConversionStats',
        'as' => 'leads.conversion_stats',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
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
    'leads.followups.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/followups',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@index',
        'as' => 'leads.followups.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/followups',
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
    'leads.followups.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/followups/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@myFollowups',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@myFollowups',
        'as' => 'leads.followups.dashboard',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/followups',
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
    'leads.followups.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/followups',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@store',
        'as' => 'leads.followups.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/followups',
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
    'leads.followups.complete' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/followups/{id}/complete',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@complete',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@complete',
        'as' => 'leads.followups.complete',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/followups',
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
    'leads.followups.reschedule' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/followups/{id}/reschedule',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@reschedule',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@reschedule',
        'as' => 'leads.followups.reschedule',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/followups',
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
    'leads.followups.cancel' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/followups/{id}/cancel',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@cancel',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@cancel',
        'as' => 'leads.followups.cancel',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/followups',
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
    'leads.followups.get' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/{leadId}/followups',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@getLeadFollowups',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@getLeadFollowups',
        'as' => 'leads.followups.get',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
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
    'leads.analytics.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/analytics',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAnalyticsController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAnalyticsController@index',
        'as' => 'leads.analytics.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/analytics',
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
    'leads.analytics.trends' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/analytics/trends',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAnalyticsController@getTrends',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAnalyticsController@getTrends',
        'as' => 'leads.analytics.trends',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/analytics',
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
    'leads.analytics.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/analytics/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAnalyticsController@export',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAnalyticsController@export',
        'as' => 'leads.analytics.export',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/analytics',
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
    'leads.analytics.compare' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/analytics/compare-agents',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAnalyticsController@compareAgents',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAnalyticsController@compareAgents',
        'as' => 'leads.analytics.compare',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/analytics',
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
    'leads.notes.delete' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/notes/delete/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@leaddeleteNotes',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@leaddeleteNotes',
        'as' => 'leads.notes.delete',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
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
    'leads.pin' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/pin/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@leadPin',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@leadPin',
        'as' => 'leads.pin',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
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
    'get-notedetail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-notedetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@getnotedetail',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@getnotedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'get-notedetail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'email.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'email_templates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'email.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'email.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'email_templates/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@create',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'email.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'email.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'email_templates/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'email.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'edit_email_template' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'edit_email_template/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@editEmailTemplate',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@editEmailTemplate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'edit_email_template',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'edit_email_template.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'edit_email_template',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@editEmailTemplate',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@editEmailTemplate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'edit_email_template.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'api' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api-key',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@editapi',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@editapi',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'api',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'api.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api-key',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@editapi',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@editapi',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'api.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.clientsmatterslist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clientsmatterslist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@clientsmatterslist',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@clientsmatterslist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.clientsmatterslist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.clientsemaillist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clientsemaillist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@clientsemaillist',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@clientsemaillist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.clientsemaillist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@edit',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@edit',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveSection' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/save-section',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@saveSection',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@saveSection',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveSection',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.partnerEoiData' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/partner-eoi-data/{partnerId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@getPartnerEoiData',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@getPartnerEoiData',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.partnerEoiData',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
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
      'uri' => 'clients/phone/send-otp',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\PhoneVerificationController@sendOTP',
        'controller' => 'App\\Http\\Controllers\\CRM\\PhoneVerificationController@sendOTP',
        'as' => 'clients.phone.sendOTP',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/phone',
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
      'uri' => 'clients/phone/verify-otp',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\PhoneVerificationController@verifyOTP',
        'controller' => 'App\\Http\\Controllers\\CRM\\PhoneVerificationController@verifyOTP',
        'as' => 'clients.phone.verifyOTP',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/phone',
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
      'uri' => 'clients/phone/resend-otp',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\PhoneVerificationController@resendOTP',
        'controller' => 'App\\Http\\Controllers\\CRM\\PhoneVerificationController@resendOTP',
        'as' => 'clients.phone.resendOTP',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/phone',
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
      'uri' => 'clients/phone/status/{contactId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\PhoneVerificationController@getStatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\PhoneVerificationController@getStatus',
        'as' => 'clients.phone.status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/phone',
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
      'uri' => 'clients/email/send-verification',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailVerificationController@sendVerificationEmail',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailVerificationController@sendVerificationEmail',
        'as' => 'clients.email.sendVerification',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/email',
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
      'uri' => 'clients/email/resend-verification',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailVerificationController@resendVerificationEmail',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailVerificationController@resendVerificationEmail',
        'as' => 'clients.email.resendVerification',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/email',
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
      'uri' => 'clients/email/status/{emailId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailVerificationController@getStatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailVerificationController@getStatus',
        'as' => 'clients.email.status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/email',
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
    'generated::XnL8ROtnFsWEWDaX' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/followup/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@followupstore',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@followupstore',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::XnL8ROtnFsWEWDaX',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::a27JLqiL8leAZRqN' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/followup/retagfollowup',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@retagfollowup',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@retagfollowup',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::a27JLqiL8leAZRqN',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::HsmMXEk4gV8OJYkF' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/changetype/{id}/{type}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@changetype',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@changetype',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::HsmMXEk4gV8OJYkF',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::nak3hJrVxdWjjqsF' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'document/download/pdf/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@downloadpdf',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@downloadpdf',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::nak3hJrVxdWjjqsF',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::fsmrZBO1Mp8GmZIv' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/removetag',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@removetag',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@removetag',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::fsmrZBO1Mp8GmZIv',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/detail/{client_id}/{client_unique_matter_ref_no?}/{tab?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@detail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@detail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.detail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getrecipients' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/get-recipients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getrecipients',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getrecipients',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getrecipients',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getonlyclientrecipients' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/get-onlyclientrecipients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getonlyclientrecipients',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getonlyclientrecipients',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getonlyclientrecipients',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getallclients' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/get-allclients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getallclients',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getallclients',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getallclients',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::QCMsVxY5JdnIB55r' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/change_assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@change_assignee',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@change_assignee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::QCMsVxY5JdnIB55r',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.gettemplates' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-templates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@gettemplates',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@gettemplates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.gettemplates',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.sendmail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'sendmail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@sendmail',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@sendmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.sendmail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::TJuWNlzeoyUX0CNc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'upload-mail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@uploadmail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@uploadmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::TJuWNlzeoyUX0CNc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::hkpKUHjMjfGhFvaD' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'upload-fetch-mail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@uploadfetchmail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@uploadfetchmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::hkpKUHjMjfGhFvaD',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9FeEDTI1m8qfhwMz' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'upload-sent-fetch-mail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@uploadsentfetchmail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@uploadsentfetchmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::9FeEDTI1m8qfhwMz',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.reassiginboxemail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'reassiginboxemail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@reassiginboxemail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@reassiginboxemail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.reassiginboxemail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.reassigsentemail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'reassigsentemail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@reassigsentemail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@reassigsentemail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.reassigsentemail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.listAllMattersWRTSelClient' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'listAllMattersWRTSelClient',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@listAllMattersWRTSelClient',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@listAllMattersWRTSelClient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.listAllMattersWRTSelClient',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.updatemailreadbit' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'updatemailreadbit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@updatemailreadbit',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@updatemailreadbit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.updatemailreadbit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.filter.emails' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/filter-emails',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@filterEmails',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@filterEmails',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.filter.emails',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.filter.sentmails' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/filter-sentemails',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@filterSentEmails',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@filterSentEmails',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.filter.sentmails',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'mail.enhance' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'mail/enhance',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@enhanceMessage',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@enhanceMessage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'mail.enhance',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.createnote' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'create-note',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@createnote',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@createnote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.createnote',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.updateNoteDatetime' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update-note-datetime',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@updateNoteDatetime',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@updateNoteDatetime',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.updateNoteDatetime',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getnotedetail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'getnotedetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@getnotedetail',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@getnotedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getnotedetail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.deletenote' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'deletenote',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@deletenote',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@deletenote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.deletenote',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Txt5jDgbE7mTgKIc' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'viewnotedetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@viewnotedetail',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@viewnotedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::Txt5jDgbE7mTgKIc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::npTO20OEdE6AaKim' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'viewapplicationnote',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@viewapplicationnote',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@viewapplicationnote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::npTO20OEdE6AaKim',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::zJKjMQhIGTghkEwX' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'saveprevvisa',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@saveprevvisa',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@saveprevvisa',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::zJKjMQhIGTghkEwX',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::99XbiHc6VL6zGVmP' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'saveonlineprimaryform',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@saveonlineform',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@saveonlineform',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::99XbiHc6VL6zGVmP',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::CnsWhYgejf7u5Ge4' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'saveonlinesecform',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@saveonlineform',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@saveonlineform',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::CnsWhYgejf7u5Ge4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YYUbZAVRD8zKBtA2' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'saveonlinechildform',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@saveonlineform',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@saveonlineform',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::YYUbZAVRD8zKBtA2',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getnotes' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-notes',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@getnotes',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@getnotes',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getnotes',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::1aTShGeNQ8Idjpos' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'pinnote',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@pinnote',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@pinnote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::1aTShGeNQ8Idjpos',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.convertActivityToNote' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'convert-activity-to-note',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@convertActivityToNote',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@convertActivityToNote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.convertActivityToNote',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.archived' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'archived',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@archived',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@archived',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.archived',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.updateclientstatus' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'change-client-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@updateclientstatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@updateclientstatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.updateclientstatus',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.activities' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-activities',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@activities',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@activities',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.activities',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.deletecostagreement' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'deletecostagreement',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@deletecostagreement',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@deletecostagreement',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.deletecostagreement',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.deleteactivitylog' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'deleteactivitylog',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@deleteactivitylog',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@deleteactivitylog',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.deleteactivitylog',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.notpickedcall' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'not-picked-call',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@notpickedcall',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@notpickedcall',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.notpickedcall',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::FJ9zNnX5oF9rxRMf' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'pinactivitylog',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@pinactivitylog',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@pinactivitylog',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::FJ9zNnX5oF9rxRMf',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::sHszJAi8jVGnQK3r' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'interested-service',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@interestedService',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@interestedService',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::sHszJAi8jVGnQK3r',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::wD9mqONFam1jSc6n' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'edit-interested-service',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@editinterestedService',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@editinterestedService',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::wD9mqONFam1jSc6n',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9IoZT2Esks1irt4T' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-services',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getServices',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getServices',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::9IoZT2Esks1irt4T',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::FMmXJUISslKrQ5R6' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'servicesavefee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@servicesavefee',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@servicesavefee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::FMmXJUISslKrQ5R6',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5YbjZqUhGiGhaqwB' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'getintrestedservice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getintrestedservice',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getintrestedservice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::5YbjZqUhGiGhaqwB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::mt1eEgTIp7nxulpT' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'getintrestedserviceedit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getintrestedserviceedit',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getintrestedserviceedit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::mt1eEgTIp7nxulpT',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getapplicationlists' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-application-lists',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getapplicationlists',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getapplicationlists',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getapplicationlists',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveapplication' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'saveapplication',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveapplication',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveapplication',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveapplication',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.convertapplication' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'convertapplication',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@convertapplication',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@convertapplication',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.convertapplication',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.deleteservices' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'deleteservices',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@deleteservices',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@deleteservices',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.deleteservices',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Ugi4a7GrpQNc8ymA' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'savetoapplication',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@savetoapplication',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@savetoapplication',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::Ugi4a7GrpQNc8ymA',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9LE1mvIHWx234U2h' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'client/createservicetaken',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@createservicetaken',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@createservicetaken',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::9LE1mvIHWx234U2h',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ogVrvryALPnrLC3S' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'client/removeservicetaken',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@removeservicetaken',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@removeservicetaken',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::ogVrvryALPnrLC3S',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::UemaRnoaYWPXgCeR' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'client/getservicetaken',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getservicetaken',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getservicetaken',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::UemaRnoaYWPXgCeR',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.addedudocchecklist' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/add-edu-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@addedudocchecklist',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@addedudocchecklist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.addedudocchecklist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.uploadedudocument' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/upload-edu-document',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@uploadedudocument',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@uploadedudocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.uploadedudocument',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.addvisadocchecklist' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/add-visa-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@addvisadocchecklist',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@addvisadocchecklist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.addvisadocchecklist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.uploadvisadocument' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/upload-visa-document',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@uploadvisadocument',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@uploadvisadocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.uploadvisadocument',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.renamedoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/rename',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@renamedoc',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@renamedoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.renamedoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.deletedocs' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/delete',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@deletedocs',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@deletedocs',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.deletedocs',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.getvisachecklist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/get-visa-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@getvisachecklist',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@getvisachecklist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.getvisachecklist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.notuseddoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/not-used',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@notuseddoc',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@notuseddoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.notuseddoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.renamechecklistdoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/rename-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@renamechecklistdoc',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@renamechecklistdoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.renamechecklistdoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.backtodoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/back-to-doc',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@backtodoc',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@backtodoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.backtodoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.download' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/download',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@download_document',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@download_document',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.download',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.addPersonalDocCategory' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/add-personal-category',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@addPersonalDocCategory',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@addPersonalDocCategory',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.addPersonalDocCategory',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.updatePersonalDocCategory' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/update-personal-category',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@updatePersonalDocCategory',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@updatePersonalDocCategory',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.updatePersonalDocCategory',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.addVisaDocCategory' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/add-visa-category',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@addVisaDocCategory',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@addVisaDocCategory',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.addVisaDocCategory',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.updateVisaDocCategory' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/update-visa-category',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@updateVisaDocCategory',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@updateVisaDocCategory',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.updateVisaDocCategory',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.eoi-roi.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/{client}/eoi-roi',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@index',
        'as' => 'clients.eoi-roi.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/{client}/eoi-roi',
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
    'clients.eoi-roi.calculatePoints' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/{client}/eoi-roi/calculate-points',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@calculatePoints',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@calculatePoints',
        'as' => 'clients.eoi-roi.calculatePoints',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/{client}/eoi-roi',
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
    'clients.eoi-roi.upsert' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/{client}/eoi-roi',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@upsert',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@upsert',
        'as' => 'clients.eoi-roi.upsert',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/{client}/eoi-roi',
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
    'clients.eoi-roi.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/{client}/eoi-roi/{eoiReference}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@show',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@show',
        'as' => 'clients.eoi-roi.show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/{client}/eoi-roi',
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
    'clients.eoi-roi.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'clients/{client}/eoi-roi/{eoiReference}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@destroy',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@destroy',
        'as' => 'clients.eoi-roi.destroy',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/{client}/eoi-roi',
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
    'clients.eoi-roi.revealPassword' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/{client}/eoi-roi/{eoiReference}/reveal-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@revealPassword',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@revealPassword',
        'as' => 'clients.eoi-roi.revealPassword',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/{client}/eoi-roi',
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
    'clients.saveaccountreport' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/saveaccountreport/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveaccountreport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveaccountreport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveaccountreport',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveaccountreport.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/saveaccountreport',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveaccountreport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveaccountreport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveaccountreport.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveinvoicereport' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/saveinvoicereport/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveinvoicereport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveinvoicereport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveinvoicereport',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveinvoicereport.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/saveinvoicereport',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveinvoicereport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveinvoicereport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveinvoicereport.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveadjustinvoicereport' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/saveadjustinvoicereport/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveadjustinvoicereport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveadjustinvoicereport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveadjustinvoicereport',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveadjustinvoicereport.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/saveadjustinvoicereport',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveadjustinvoicereport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveadjustinvoicereport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveadjustinvoicereport.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveofficereport' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/saveofficereport/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveofficereport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveofficereport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveofficereport',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveofficereport.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/saveofficereport',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveofficereport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveofficereport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveofficereport.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.savejournalreport' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/savejournalreport/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@savejournalreport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@savejournalreport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.savejournalreport',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.savejournalreport.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/savejournalreport',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@savejournalreport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@savejournalreport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.savejournalreport.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.isAnyInvoiceNoExistInDB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/isAnyInvoiceNoExistInDB',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@isAnyInvoiceNoExistInDB',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@isAnyInvoiceNoExistInDB',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.isAnyInvoiceNoExistInDB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.listOfInvoice' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/listOfInvoice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@listOfInvoice',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@listOfInvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.listOfInvoice',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getTopReceiptValInDB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/getTopReceiptValInDB',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getTopReceiptValInDB',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getTopReceiptValInDB',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getTopReceiptValInDB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getInfoByReceiptId' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/getInfoByReceiptId',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getInfoByReceiptId',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getInfoByReceiptId',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getInfoByReceiptId',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Zi9Af5Ee4qIJIkAx' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/genInvoice/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@genInvoice',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@genInvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::Zi9Af5Ee4qIJIkAx',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.sendToHubdoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/sendToHubdoc/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@sendToHubdoc',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@sendToHubdoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.sendToHubdoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.checkHubdocStatus' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/checkHubdocStatus/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@checkHubdocStatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@checkHubdocStatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.checkHubdocStatus',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::sz3HxHnWXUCAod8k' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/printPreview/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@printPreview',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@printPreview',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::sz3HxHnWXUCAod8k',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getTopInvoiceNoFromDB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/getTopInvoiceNoFromDB',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getTopInvoiceNoFromDB',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getTopInvoiceNoFromDB',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getTopInvoiceNoFromDB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.clientLedgerBalanceAmount' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/clientLedgerBalanceAmount',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@clientLedgerBalanceAmount',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@clientLedgerBalanceAmount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.clientLedgerBalanceAmount',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.invoicelist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/invoicelist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@invoicelist',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@invoicelist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.invoicelist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
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
      'uri' => 'void_invoice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@void_invoice',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@void_invoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
    'clients.clientreceiptlist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/clientreceiptlist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@clientreceiptlist',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@clientreceiptlist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.clientreceiptlist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.officereceiptlist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/officereceiptlist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@officereceiptlist',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@officereceiptlist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.officereceiptlist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.journalreceiptlist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/journalreceiptlist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@journalreceiptlist',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@journalreceiptlist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.journalreceiptlist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
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
      'uri' => 'validate_receipt',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@validate_receipt',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@validate_receipt',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
    'generated::DBerHGDcNV5f08oe' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'delete_receipt',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@delete_receipt',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@delete_receipt',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::DBerHGDcNV5f08oe',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::FRdsIMAunYiVsK1G' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/genClientFundLedgerInvoice/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@genClientFundLedgerInvoice',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@genClientFundLedgerInvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::FRdsIMAunYiVsK1G',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::LqTBTEUjacGqvDJu' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/genofficereceiptInvoice/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@genofficereceiptInvoice',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@genofficereceiptInvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::LqTBTEUjacGqvDJu',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.update-client-funds-ledger' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update-client-funds-ledger',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@updateClientFundsLedger',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@updateClientFundsLedger',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.update-client-funds-ledger',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.invoiceamount' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/invoiceamount',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getInvoiceAmount',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getInvoiceAmount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.invoiceamount',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.updateAddress' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/update-address',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@updateAddress',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@updateAddress',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.updateAddress',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.searchAddressFull' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/search-address-full',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@searchAddressFull',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@searchAddressFull',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.searchAddressFull',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getPlaceDetails' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/get-place-details',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@getPlaceDetails',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@getPlaceDetails',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getPlaceDetails',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::GeZU6wg2mxDuTJye' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'address_auto_populate',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@address_auto_populate',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@address_auto_populate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::GeZU6wg2mxDuTJye',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::QsfeWpaMUlGgWe0V' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/fetchClientContactNo',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@fetchClientContactNo',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@fetchClientContactNo',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::QsfeWpaMUlGgWe0V',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.clientdetailsinfo' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/clientdetailsinfo/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@clientdetailsinfo',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@clientdetailsinfo',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.clientdetailsinfo',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.clientdetailsinfo.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/clientdetailsinfo',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@clientdetailsinfo',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@clientdetailsinfo',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.clientdetailsinfo.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'getVisaTypes' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-visa-types',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@getVisaTypes',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@getVisaTypes',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'getVisaTypes',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'getCountries' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-countries',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@getCountries',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@getCountries',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'getCountries',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.updateOccupation' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'updateOccupation',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@updateOccupation',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@updateOccupation',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.updateOccupation',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.updateOccupation' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/updateOccupation',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@updateOccupation',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@updateOccupation',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'leads.updateOccupation',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.searchPartner' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/search-partner',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@searchPartner',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@searchPartner',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.searchPartner',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.searchPartnerTest' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/search-partner-test',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@searchPartnerTest',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@searchPartnerTest',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.searchPartnerTest',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.testBidirectional' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/test-bidirectional',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@testBidirectionalRemoval',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@testBidirectionalRemoval',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.testBidirectional',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveRelationship' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/save-relationship',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@saveRelationship',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@saveRelationship',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveRelationship',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.generateagreement' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/generateagreement',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@generateagreement',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@generateagreement',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.generateagreement',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getMigrationAgentDetail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/getMigrationAgentDetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getMigrationAgentDetail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getMigrationAgentDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getMigrationAgentDetail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getVisaAggreementMigrationAgentDetail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/getVisaAggreementMigrationAgentDetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getVisaAggreementMigrationAgentDetail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getVisaAggreementMigrationAgentDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getVisaAggreementMigrationAgentDetail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getCostAssignmentMigrationAgentDetail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/getCostAssignmentMigrationAgentDetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getCostAssignmentMigrationAgentDetail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getCostAssignmentMigrationAgentDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getCostAssignmentMigrationAgentDetail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.savecostassignment' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/savecostassignment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@savecostassignment',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@savecostassignment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.savecostassignment',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::e5A4ZpgwWrtpaxD1' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/check-cost-assignment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@checkCostAssignment',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@checkCostAssignment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::e5A4ZpgwWrtpaxD1',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.savecostassignmentlead' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/savecostassignmentlead',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@savecostassignmentlead',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@savecostassignmentlead',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.savecostassignmentlead',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
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
      'uri' => 'clients/getCostAssignmentMigrationAgentDetailLead',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getCostAssignmentMigrationAgentDetailLead',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getCostAssignmentMigrationAgentDetailLead',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
    'clients.uploadAgreement' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/{admin}/upload-agreement',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@uploadAgreement',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@uploadAgreement',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.uploadAgreement',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
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
      'uri' => 'forms',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Form956Controller@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\Form956Controller@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'forms/{form}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Form956Controller@show',
        'controller' => 'App\\Http\\Controllers\\CRM\\Form956Controller@show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'forms/{form}/preview',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Form956Controller@previewPdf',
        'controller' => 'App\\Http\\Controllers\\CRM\\Form956Controller@previewPdf',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'forms/{form}/pdf',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Form956Controller@generatePdf',
        'controller' => 'App\\Http\\Controllers\\CRM\\Form956Controller@generatePdf',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
    'clients.getmattertemplates' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-matter-templates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getmattertemplates',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getmattertemplates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getmattertemplates',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getClientMatters' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-client-matters/{clientId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getClientMatters',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getClientMatters',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getClientMatters',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::moSUG3T3V6g2gKuk' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/fetchClientMatterAssignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@fetchClientMatterAssignee',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@fetchClientMatterAssignee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::moSUG3T3V6g2gKuk',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::pbSpncNDcSZOCpmK' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/updateClientMatterAssignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@updateClientMatterAssignee',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@updateClientMatterAssignee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::pbSpncNDcSZOCpmK',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'upload_checklists.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'upload-checklists',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\UploadChecklistController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\UploadChecklistController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'upload_checklists.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'upload_checklists.matter' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'upload-checklists/matter/{matterId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\UploadChecklistController@showByMatter',
        'controller' => 'App\\Http\\Controllers\\CRM\\UploadChecklistController@showByMatter',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'upload_checklists.matter',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'upload_checklistsupload' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'upload-checklists/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\UploadChecklistController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\UploadChecklistController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'upload_checklistsupload',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::IYvM0B4S8WPDk7d5' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/personalfollowup/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@personalfollowup',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@personalfollowup',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::IYvM0B4S8WPDk7d5',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::coMRnYEwXN1GvRbb' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/updatefollowup/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@updatefollowup',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@updatefollowup',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::coMRnYEwXN1GvRbb',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::GGS6tLXXkyg3WTK0' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/reassignfollowup/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@reassignfollowupstore',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@reassignfollowupstore',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::GGS6tLXXkyg3WTK0',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.updatesessioncompleted' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/update-session-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@updatesessioncompleted',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@updatesessioncompleted',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.updatesessioncompleted',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getAllUser' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/getAllUser',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getAllUser',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getAllUser',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getAllUser',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.toggleClientPortal' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/toggle-client-portal',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPortalController@toggleClientPortal',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPortalController@toggleClientPortal',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.toggleClientPortal',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'anzsco.search' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'anzsco/search',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@search',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@search',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'anzsco.search',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
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
      'uri' => 'check-email',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@checkEmail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@checkEmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'check.phone',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@checkContact',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@checkContact',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
    'generated::42T8rX2P2rBcOvYz' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'save_tag',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@save_tag',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@save_tag',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::42T8rX2P2rBcOvYz',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
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
      'uri' => 'save-references',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@savereferences',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@savereferences',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'check-star-client',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@checkStarClient',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@checkStarClient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'merge_records',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@merge_records',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@merge_records',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
    'send-webhook' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'send-webhook',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@sendToWebhook',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@sendToWebhook',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'send-webhook',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::abevjveklLkzlX7d' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fetch-visa_expiry_messages',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchvisaexpirymessages',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchvisaexpirymessages',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::abevjveklLkzlX7d',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.email.verify' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'verify-email/{token}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailVerificationController@verifyEmail',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailVerificationController@verifyEmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.email.verify',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'applications.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'applications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'applications.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'applications.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'applications/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@create',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'applications.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'applications.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'applications/detail/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@detail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@detail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'applications.detail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'applications.import' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'applications-import',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@import',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@import',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'applications.import',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5ILg5C0x7I21CF7Z' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'getapplicationdetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplicationdetail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplicationdetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::5ILg5C0x7I21CF7Z',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::TilzQ08M9E6lFXoT' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'load-application-insert-update-data',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@loadApplicationInsertUpdateData',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@loadApplicationInsertUpdateData',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::TilzQ08M9E6lFXoT',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::tBhSRcoNFikujDGy' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'updatestage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updatestage',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updatestage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::tBhSRcoNFikujDGy',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::McsRmlwfkezR53Ez' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'completestage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@completestage',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@completestage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::McsRmlwfkezR53Ez',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::B7bsTcLNaiG9GW5m' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'updatebackstage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updatebackstage',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updatebackstage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::B7bsTcLNaiG9GW5m',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::XQ2f6qXKtadNAZIw' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-applications-logs',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplicationslogs',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplicationslogs',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::XQ2f6qXKtadNAZIw',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::pM3n0wGxquUjJ8W2' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-applications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplications',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplications',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::pM3n0wGxquUjJ8W2',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::vfv4ci04yBJPAykb' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'discontinue_application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@discontinue_application',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@discontinue_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::vfv4ci04yBJPAykb',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::BeAyymbKS96HhQ2l' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'revert_application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@revert_application',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@revert_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::BeAyymbKS96HhQ2l',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::tvn3QaErXJvZfKRn' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'create-app-note',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@addNote',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@addNote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::tvn3QaErXJvZfKRn',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::UL1KgsZUAlQSqlv2' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'getapplicationnotes',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplicationnotes',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplicationnotes',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::UL1KgsZUAlQSqlv2',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::VlaADIrgNJ9iwsbc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'application-sendmail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@applicationsendmail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@applicationsendmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::VlaADIrgNJ9iwsbc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::kmvBzMFbXmLqSaZi' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'application/updateintake',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updateintake',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updateintake',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::kmvBzMFbXmLqSaZi',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::HIRnHRHVZWqODFKU' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'application/updatedates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updatedates',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updatedates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::HIRnHRHVZWqODFKU',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::jbzWzEcY8pymrd98' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'application/updateexpectwin',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updateexpectwin',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updateexpectwin',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::jbzWzEcY8pymrd98',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::OuR235WSgnIEL7u9' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'application/getapplicationbycid',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplicationbycid',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplicationbycid',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::OuR235WSgnIEL7u9',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::SY4xKjaG8puugULE' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'application/spagent_application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@spagent_application',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@spagent_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::SY4xKjaG8puugULE',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::GeNBCUenYwjYfK2h' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'application/sbagent_application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@sbagent_application',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@sbagent_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::GeNBCUenYwjYfK2h',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::wW2xYvXfXXnGFIl1' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'application/application_ownership',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@application_ownership',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@application_ownership',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::wW2xYvXfXXnGFIl1',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::m5RmPWQC9In1iJDY' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'superagent',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@superagent',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@superagent',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::m5RmPWQC9In1iJDY',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::EIpA4iyusrRs4IBQ' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'subagent',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@subagent',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@subagent',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::EIpA4iyusrRs4IBQ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::1BqwlKu79oU3BpEg' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'applicationsavefee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@applicationsavefee',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@applicationsavefee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::1BqwlKu79oU3BpEg',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::TSmJwShUdIbgL7A4' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'application/export/pdf/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@exportapplicationpdf',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@exportapplicationpdf',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::TSmJwShUdIbgL7A4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::apvXtGD1hvJZsvTF' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'add-checklists',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@addchecklists',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@addchecklists',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::apvXtGD1hvJZsvTF',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::wwtbUA55UOHn1EPC' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'application/checklistupload',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@checklistupload',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@checklistupload',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::wwtbUA55UOHn1EPC',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::RH6DVzgYsUBEEd0r' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'deleteapplicationdocs',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@deleteapplicationdocs',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@deleteapplicationdocs',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::RH6DVzgYsUBEEd0r',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ronlJzovTApXOGaa' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'application/publishdoc',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@publishdoc',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@publishdoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::ronlJzovTApXOGaa',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'migration.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'migration',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@migrationindex',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@migrationindex',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'migration.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'officevisits.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'office-visits',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'officevisits.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'officevisits.waiting' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'office-visits/waiting',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@waiting',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@waiting',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'officevisits.waiting',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'officevisits.attending' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'office-visits/attending',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@attending',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@attending',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'officevisits.attending',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'officevisits.completed' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'office-visits/completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@completed',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@completed',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'officevisits.completed',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'officevisits.archived' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'office-visits/archived',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@archived',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@archived',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'officevisits.archived',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'officevisits.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'office-visits/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@create',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'officevisits.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::nu5ReDEjjF1712X3' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'checkin',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@checkin',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@checkin',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::nu5ReDEjjF1712X3',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::vr5ADKLsAzMHYLa3' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-checkin-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@getcheckin',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@getcheckin',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::vr5ADKLsAzMHYLa3',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Re5Dem3fFd3po16S' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update_visit_purpose',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@update_visit_purpose',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@update_visit_purpose',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::Re5Dem3fFd3po16S',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::E6XW2IhNoFEUXzE7' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update_visit_comment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@update_visit_comment',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@update_visit_comment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::E6XW2IhNoFEUXzE7',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::OaXOo5Cifh6fS9ue' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'attend_session',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@attend_session',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@attend_session',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::OaXOo5Cifh6fS9ue',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::CxR0SycsexJMOOKO' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'complete_session',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@complete_session',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@complete_session',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::CxR0SycsexJMOOKO',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::bP51QVyRogAAeywy' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'office-visits/change_assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@change_assignee',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@change_assignee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::bP51QVyRogAAeywy',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
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
      'uri' => 'appointments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'appointments.index',
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'appointments/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'appointments.create',
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@create',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'appointments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'appointments.store',
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'appointments/{appointment}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'appointments.show',
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@show',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'appointments/{appointment}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'appointments.edit',
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@edit',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'appointments/{appointment}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'appointments.update',
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'appointments/{appointment}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'appointments.destroy',
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@destroy',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@destroy',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'appointments-adelaide',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsAdelaide',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsAdelaide',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
    'generated::jRCV0YywhUw0zzjf' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'deleteappointment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@deleteappointment',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@deleteappointment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::jRCV0YywhUw0zzjf',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::x5I1Xwti1hxjrqcs' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'add-appointment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@addAppointment',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@addAppointment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::x5I1Xwti1hxjrqcs',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::SWSwCLNpOSCMelXU' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'add-appointment-book',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@addAppointmentBook',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@addAppointmentBook',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::SWSwCLNpOSCMelXU',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::1pPIS80rCVSwBGQV' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'editappointment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@editappointment',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@editappointment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::1pPIS80rCVSwBGQV',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ThLT4fTLIT4pVkgb' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'updatefollowupschedule',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@updatefollowupschedule',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@updatefollowupschedule',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::ThLT4fTLIT4pVkgb',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::QE2kwoCTK2mr5bTm' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'updateappointmentstatus/{status}/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@updateappointmentstatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@updateappointmentstatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::QE2kwoCTK2mr5bTm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::48SGU6rBnF3fkR8d' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update_appointment_status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update_appointment_status',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update_appointment_status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::48SGU6rBnF3fkR8d',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::kpDPWRlazljRJ1aC' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update_appointment_priority',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update_appointment_priority',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update_appointment_priority',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::kpDPWRlazljRJ1aC',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::3mmVro7rm1V9IWYX' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update_apppointment_comment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update_apppointment_comment',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update_apppointment_comment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::3mmVro7rm1V9IWYX',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::FcbH3iNsmePLh2Z0' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update_apppointment_description',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update_apppointment_description',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update_apppointment_description',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::FcbH3iNsmePLh2Z0',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::jnIWXgrBxlKi9U3M' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-appointments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@getAppointments',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@getAppointments',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::jnIWXgrBxlKi9U3M',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Uz0FuoFIPUt3PTNm' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'getAppointmentdetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@getAppointmentdetail',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@getAppointmentdetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::Uz0FuoFIPUt3PTNm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::x1hohSJOtQTk7eZ0' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-assigne-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@assignedetail',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@assignedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::x1hohSJOtQTk7eZ0',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::HTqoJ0O5Yw7dBpgs' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'change_assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@change_assignee',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@change_assignee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::HTqoJ0O5Yw7dBpgs',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
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
      'uri' => 'getdatetimebackend',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\HomeController@getdatetimebackend',
        'controller' => 'App\\Http\\Controllers\\HomeController@getdatetimebackend',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'getdisableddatetime',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\HomeController@getdisableddatetime',
        'controller' => 'App\\Http\\Controllers\\HomeController@getdisableddatetime',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'booking/appointments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@index',
        'as' => 'booking.appointments.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
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
      'uri' => 'booking/appointments/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@show',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@show',
        'as' => 'booking.appointments.show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
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
      'uri' => 'booking/appointments/{id}/json',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@getAppointmentJson',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@getAppointmentJson',
        'as' => 'booking.appointments.json',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
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
      'uri' => 'booking/calendar/{type}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@calendar',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@calendar',
        'as' => 'booking.appointments.calendar',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
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
      'uri' => 'booking/appointments/{id}/update-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@updateStatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@updateStatus',
        'as' => 'booking.appointments.update-status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
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
      'uri' => 'booking/appointments/{id}/update-consultant',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@updateConsultant',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@updateConsultant',
        'as' => 'booking.appointments.update-consultant',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
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
      'uri' => 'booking/appointments/{id}/add-note',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@addNote',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@addNote',
        'as' => 'booking.appointments.add-note',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
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
      'uri' => 'booking/appointments/{id}/update-followup',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@updateFollowUp',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@updateFollowUp',
        'as' => 'booking.appointments.update-followup',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
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
      'uri' => 'booking/appointments/{id}/send-reminder',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@sendReminder',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@sendReminder',
        'as' => 'booking.appointments.send-reminder',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
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
      'uri' => 'booking/appointments/bulk-update-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@bulkUpdateStatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@bulkUpdateStatus',
        'as' => 'booking.appointments.bulk-update-status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
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
      'uri' => 'booking/appointments/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@export',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@export',
        'as' => 'booking.appointments.export',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
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
      'uri' => 'booking/sync/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@syncDashboard',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@syncDashboard',
        'as' => 'booking.sync.dashboard',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
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
      'uri' => 'booking/sync/stats',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@syncStats',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@syncStats',
        'as' => 'booking.sync.stats',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
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
      'uri' => 'booking/sync/manual',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
          2 => 'can:trigger-manual-sync',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@manualSync',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@manualSync',
        'as' => 'booking.sync.manual',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
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
      'uri' => 'booking/api/appointments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@getAppointments',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@getAppointments',
        'as' => 'booking.api.appointments',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
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
    'auditlogs.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'audit-logs',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AuditLogController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\AuditLogController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'auditlogs.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::w1vOAhbKElypUhsZ' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fetch-notification',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchnotification',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchnotification',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::w1vOAhbKElypUhsZ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::1LQ0sdBKmJXavkQG' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fetch-messages',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchmessages',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchmessages',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::1LQ0sdBKmJXavkQG',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::XOGnHcuQ3FJXnW1O' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fetch-office-visit-notifications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchOfficeVisitNotifications',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchOfficeVisitNotifications',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::XOGnHcuQ3FJXnW1O',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::1z26eBfW3JDP50K9' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'mark-notification-seen',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@markNotificationSeen',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@markNotificationSeen',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::1z26eBfW3JDP50K9',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9VXMgaiYCespmPgj' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'check-checkin-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@checkCheckinStatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@checkCheckinStatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::9VXMgaiYCespmPgj',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::UwBS9tZuZekW90ce' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update-checkin-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@updateCheckinStatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@updateCheckinStatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::UwBS9tZuZekW90ce',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::eJI0oXSDmqzjmN92' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'all-notifications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@allnotification',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@allnotification',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::eJI0oXSDmqzjmN92',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::lbm3JxLkHJWpmTDn' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fetch-InPersonWaitingCount',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchInPersonWaitingCount',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchInPersonWaitingCount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::lbm3JxLkHJWpmTDn',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Tim8bmFkaPJG5GZi' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fetch-TotalActivityCount',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchTotalActivityCount',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchTotalActivityCount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::Tim8bmFkaPJG5GZi',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
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
      'uri' => 'assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'assignee.index',
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/',
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
      'uri' => 'assignee/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'assignee.create',
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@create',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/',
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
      'uri' => 'assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'assignee.store',
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/',
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
      'uri' => 'assignee/{assignee}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'assignee.show',
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@show',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/',
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
      'uri' => 'assignee/{assignee}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'assignee.edit',
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@edit',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/',
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
      'uri' => 'assignee/{assignee}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'assignee.update',
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@update',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/',
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
      'uri' => 'assignee/{assignee}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'assignee.destroy',
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/',
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
    'generated::0Eryx64UdCmIZHvH' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'assignee-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@completed',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@completed',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::0Eryx64UdCmIZHvH',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::7TBZnAj523c1CWXM' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update-task-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@updatetaskcompleted',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@updatetaskcompleted',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::7TBZnAj523c1CWXM',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8nftBvTfNs6fkOjE' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update-task-not-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@updatetasknotcompleted',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@updatetasknotcompleted',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::8nftBvTfNs6fkOjE',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
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
      'uri' => 'assigned_by_me',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@assigned_by_me',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@assigned_by_me',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'assigned_to_me',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@assigned_to_me',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@assigned_to_me',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'destroy_by_me/{note_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy_by_me',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy_by_me',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'destroy_to_me/{note_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy_to_me',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy_to_me',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'action_completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@action_completed',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@action_completed',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'destroy_activity/{note_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy_activity',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy_activity',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'destroy_complete_activity/{note_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy_complete_activity',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy_complete_activity',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
    'generated::87pqsLlVqDtzHUZX' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'is_email_unique',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@is_email_unique',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@is_email_unique',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::87pqsLlVqDtzHUZX',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5PknYMVEGeheO4Je' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'is_contactno_unique',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@is_contactno_unique',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@is_contactno_unique',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::5PknYMVEGeheO4Je',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::C6FtbkJ7HFfhNj8B' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'extenddeadlinedate',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@extenddeadlinedate',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@extenddeadlinedate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::C6FtbkJ7HFfhNj8B',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::pM0sGFwy0ePa17C5' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update-stage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@updateStage',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@updateStage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::pM0sGFwy0ePa17C5',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9XFhqr95Y1dQiroI' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'get_assignee_list',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@get_assignee_list',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@get_assignee_list',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::9XFhqr95Y1dQiroI',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ldCOvQkNdiCzWmpt' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update-task',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@updateTask',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@updateTask',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::ldCOvQkNdiCzWmpt',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
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
      'uri' => 'action/counts',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@getActionCounts',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@getActionCounts',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@action',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@action',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
      'uri' => 'action/list',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@getAction',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@getAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
    'test.signature' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'test-signature',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:53:"function () {
    return \\view(\'test-signature\');
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000b860000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
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
    'doc-to-pdf.form' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'doc-to-pdf',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@showForm',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@showForm',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'doc-to-pdf.form',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'doc-to-pdf.convert' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'doc-to-pdf/convert',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@convertLocal',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@convertLocal',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'doc-to-pdf.convert',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'doc-to-pdf.test' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'doc-to-pdf/test',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@testLocalConversion',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@testLocalConversion',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'doc-to-pdf.test',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'doc-to-pdf.test-python' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'doc-to-pdf/test-python',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@testPythonConversion',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@testPythonConversion',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'doc-to-pdf.test-python',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'doc-to-pdf.debug' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'doc-to-pdf/debug',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@debugConfig',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@debugConfig',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'doc-to-pdf.debug',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'signatures',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'signatures/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@create',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.suggest-association' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures/suggest-association',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@suggestAssociation',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@suggestAssociation',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.suggest-association',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.preview-email' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures/preview-email',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@previewEmail',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@previewEmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.preview-email',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.bulk-archive' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures/bulk-archive',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@bulkArchive',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@bulkArchive',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.bulk-archive',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.bulk-void' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures/bulk-void',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@bulkVoid',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@bulkVoid',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.bulk-void',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.bulk-resend' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures/bulk-resend',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@bulkResend',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@bulkResend',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.bulk-resend',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'signatures/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@show',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.show',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.reminder' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures/{id}/reminder',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@sendReminder',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@sendReminder',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.reminder',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.send' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures/{id}/send',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@sendForSignature',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@sendForSignature',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.send',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.copy-link' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'signatures/{id}/copy-link',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@copyLink',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@copyLink',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.copy-link',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.associate' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures/{id}/associate',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@associate',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@associate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.associate',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.client-matters' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'signatures/api/client-matters/{clientId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@getClientMatters',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@getClientMatters',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.client-matters',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.detach' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures/{id}/detach',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@detach',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@detach',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.detach',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.matters' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/{id}/matters',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@getClientMatters',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@getClientMatters',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.matters',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
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
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:1397:"function($id, $page) {
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
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000d010000000000000000";}}',
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
    'documents.sign' => 
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
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@sign',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@sign',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.sign',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.submitSignatures' => 
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
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@submitSignatures',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@submitSignatures',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.submitSignatures',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.page' => 
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
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@getPage',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@getPage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.page',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.index' => 
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
          1 => 'auth:admin',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:160:"function($id = null) {
    if ($id) {
        return \\redirect()->route(\'signatures.show\', $id);
    }
    return \\redirect()->route(\'signatures.index\');
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000d290000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.index',
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
    'documents.download.signed' => 
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
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@downloadSigned',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@downloadSigned',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.download.signed',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.download_and_thankyou' => 
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
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@downloadSignedAndThankyou',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@downloadSignedAndThankyou',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.download_and_thankyou',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.thankyou' => 
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
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@thankyou',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@thankyou',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.thankyou',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.sendReminder' => 
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
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@sendReminder',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@sendReminder',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.sendReminder',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@create',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/{id}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@edit',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.update' => 
    array (
      'methods' => 
      array (
        0 => 'PATCH',
      ),
      'uri' => 'documents/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@update',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.sendSigningLink' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/{document}/send-signing-link',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@sendSigningLink',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@sendSigningLink',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.sendSigningLink',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.showSignForm' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/{document}/sign',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@showSignForm',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@showSignForm',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.showSignForm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
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
    'generated::CD53pecQS9juZBCV' => 
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
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000d1b0000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::CD53pecQS9juZBCV',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::aSFOl8rLmdhY6Uz7' => 
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
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000d300000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::aSFOl8rLmdhY6Uz7',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::qCGrZvtGIqUdFAEt' => 
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
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000d320000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::qCGrZvtGIqUdFAEt',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::LYYGc10NNSdlCCpu' => 
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
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000d340000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::LYYGc10NNSdlCCpu',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::3dZ4xFV2cbmJtCVw' => 
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
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000d360000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::3dZ4xFV2cbmJtCVw',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::LNrCGvbfTrHtlnnM' => 
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
        'as' => 'generated::LNrCGvbfTrHtlnnM',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
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
