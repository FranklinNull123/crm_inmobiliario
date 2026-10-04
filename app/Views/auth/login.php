<?php
declare(strict_types=1);
/** @var bool $setupRequired */
/** @var string $csrfToken */
/** @var string|null $authError */
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $setupRequired ? 'Configurar administrador' : 'Iniciar sesión' ?> · Inmobi CRM</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'DM Sans', sans-serif; }
    h1, h2 { font-family: 'Manrope', sans-serif; }
  </style>
</head>
<body class="min-h-screen bg-[#f3f6f3] text-slate-900">
  <main class="grid min-h-screen lg:grid-cols-[1.1fr_.9fr]">
    <section class="relative hidden overflow-hidden bg-[#183b35] px-12 py-14 text-white lg:flex lg:flex-col lg:justify-between">
      <div class="absolute inset-0 opacity-20" style="background-image:linear-gradient(135deg,transparent 0 49%,#c5dfbf 49.2% 49.6%,transparent 49.8%),linear-gradient(45deg,transparent 0 49%,#c5dfbf 49.2% 49.6%,transparent 49.8%);background-size:58px 58px"></div>
      <div class="relative flex items-center gap-3"><span class="grid h-11 w-11 place-items-center rounded-xl bg-[#d4a95f] text-xl font-bold text-[#183b35]">I</span><span class="font-semibold tracking-wide">INMOBI <span class="font-normal text-white/60">/ GESTIÓN</span></span></div>
      <div class="relative max-w-xl pb-10">
        <p class="mb-5 text-sm font-semibold uppercase text-[#e8c980]">Plataforma inmobiliaria</p>
        <h1 class="text-5xl font-extrabold leading-tight">Cada proyecto,<br>en buenas manos.</h1>
        <p class="mt-6 max-w-md text-base leading-7 text-white/70">Acceso seguro para gestionar la operación comercial e inmobiliaria.</p>
      </div>
      <p class="relative text-xs text-white/50">© <?= date('Y') ?> Inmobi CRM</p>
    </section>
    <section class="flex items-center justify-center px-5 py-12 sm:px-10">
      <div class="w-full max-w-md">
        <div class="mb-8 lg:hidden"><span class="font-bold text-[#183b35]">INMOBI / GESTIÓN</span></div>
        <p class="text-sm font-semibold uppercase tracking-wide text-[#668177]">Acceso al sistema</p>
        <h2 class="mt-2 text-3xl font-bold tracking-tight"><?= $setupRequired ? 'Crea el superadministrador' : 'Bienvenido de nuevo' ?></h2>
        <p class="mt-2 text-sm leading-6 text-slate-600"><?= $setupRequired ? 'Este primer usuario administrará roles, permisos y configuración.' : 'Ingresa con las credenciales de tu cuenta.' ?></p>

        <?php if (!empty($authError)): ?>
          <div role="alert" class="mt-6 rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"><?= $escape((string) $authError) ?></div>
        <?php endif; ?>

        <form method="post" action="?view=login" class="mt-8 space-y-5">
          <input type="hidden" name="_csrf" value="<?= $escape($csrfToken) ?>">
          <?php if ($setupRequired): ?>
            <input type="hidden" name="action" value="setup_admin">
            <div>
              <label for="name" class="mb-1.5 block text-sm font-semibold">Nombre completo</label>
              <input id="name" name="name" required minlength="2" maxlength="100" autocomplete="name" class="h-12 w-full rounded-md border border-slate-300 bg-white px-3 text-sm outline-none focus:border-[#397a65] focus:ring-2 focus:ring-[#397a65]/20">
            </div>
          <?php else: ?>
            <input type="hidden" name="action" value="login">
          <?php endif; ?>
          <div>
            <label for="email" class="mb-1.5 block text-sm font-semibold">Correo electrónico</label>
            <input id="email" name="email" type="email" required maxlength="120" autocomplete="email" class="h-12 w-full rounded-md border border-slate-300 bg-white px-3 text-sm outline-none focus:border-[#397a65] focus:ring-2 focus:ring-[#397a65]/20">
          </div>
          <div>
            <label for="password" class="mb-1.5 block text-sm font-semibold">Contraseña</label>
            <input id="password" name="password" type="password" required minlength="<?= $setupRequired ? '12' : '1' ?>" maxlength="4096" autocomplete="<?= $setupRequired ? 'new-password' : 'current-password' ?>" class="h-12 w-full rounded-md border border-slate-300 bg-white px-3 text-sm outline-none focus:border-[#397a65] focus:ring-2 focus:ring-[#397a65]/20">
            <?php if ($setupRequired): ?><p class="mt-1.5 text-xs text-slate-500">Usa al menos 12 caracteres.</p><?php endif; ?>
          </div>
          <button class="flex h-12 w-full items-center justify-center rounded-md bg-[#183b35] px-4 text-sm font-semibold text-white hover:bg-[#24584d] focus:outline-none focus:ring-4 focus:ring-[#397a65]/25" type="submit">
            <?= $setupRequired ? 'Crear administrador y continuar' : 'Iniciar sesión' ?>
          </button>
        </form>
      </div>
    </section>
  </main>
</body>
</html>