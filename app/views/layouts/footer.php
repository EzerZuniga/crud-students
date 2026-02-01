<?php

declare(strict_types=1);

const APP_NAME = 'CRUD Students';
const APP_DESCRIPTION = 'Sistema de Gestión de Estudiantes';
const COPYRIGHT_SYMBOL = '&copy;';

const FOOTER_CONTAINER_CLASS = 'bg-light border-top mt-auto py-4';
const FOOTER_TEXT_CLASS = 'mb-0 text-muted';
const FOOTER_SMALL_CLASS = 'text-muted';

const ICON_MORTARBOARD = 'bi bi-mortarboard-fill me-1';
const ICON_HEART = 'bi bi-heart-fill text-danger';

const BOOTSTRAP_VERSION = '5.3.2';
const BOOTSTRAP_JS_INTEGRITY = 'sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL';
const BOOTSTRAP_CDN_BASE = 'https://cdn.jsdelivr.net/npm/bootstrap@';

const TECH_STACK = 'PHP & Bootstrap';
const DEVELOPED_WITH_TEXT = 'Desarrollado con';
const USING_TEXT = 'usando';

const COL_LEFT_CLASS = 'col-md-6 text-center text-md-start';
const COL_RIGHT_CLASS = 'col-md-6 text-center text-md-end';

const SCRIPT_TYPE_MODULE = 'module';
const CROSSORIGIN_ANONYMOUS = 'anonymous';

function getCopyrightText(): string
{
    return sprintf(
        '%s %s %s %d - %s',
        APP_NAME,
        COPYRIGHT_SYMBOL,
        '',
        (int)date('Y'),
        APP_DESCRIPTION
    );
}

function getDevelopedByText(): string
{
    return sprintf(
        '%s <i class="%s"></i> %s %s',
        DEVELOPED_WITH_TEXT,
        ICON_HEART,
        USING_TEXT,
        TECH_STACK
    );
}

function getBootstrapJsUrl(): string
{
    return sprintf(
        '%s%s/dist/js/bootstrap.bundle.min.js',
        BOOTSTRAP_CDN_BASE,
        BOOTSTRAP_VERSION
    );
}

function renderScriptTag(string $src, string $integrity, string $crossorigin): string
{
    return sprintf(
        '<script src="%s" integrity="%s" crossorigin="%s"></script>',
        htmlspecialchars($src, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($integrity, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($crossorigin, ENT_QUOTES, 'UTF-8')
    );
}

function renderCustomScript(string $path, string $type = ''): string
{
    $typeAttr = $type !== '' ? sprintf(' type="%s"', htmlspecialchars($type, ENT_QUOTES, 'UTF-8')) : '';
    return sprintf(
        '<script src="%s"%s></script>',
        htmlspecialchars($path, ENT_QUOTES, 'UTF-8'),
        $typeAttr
    );
}

?>
        </div>
    </main>

    <footer class="<?= FOOTER_CONTAINER_CLASS ?>">
        <div class="container">
            <div class="row">
                <div class="<?= COL_LEFT_CLASS ?>">
                    <p class="<?= FOOTER_TEXT_CLASS ?>">
                        <i class="<?= ICON_MORTARBOARD ?>"></i>
                        <?= getCopyrightText() ?>
                    </p>
                </div>
                <div class="<?= COL_RIGHT_CLASS ?>">
                    <small class="<?= FOOTER_SMALL_CLASS ?>">
                        <?= getDevelopedByText() ?>
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <?= renderScriptTag(getBootstrapJsUrl(), BOOTSTRAP_JS_INTEGRITY, CROSSORIGIN_ANONYMOUS) ?>
    
    <?= renderCustomScript(asset('js/main.js'), SCRIPT_TYPE_MODULE) ?>
</body>
</html>
