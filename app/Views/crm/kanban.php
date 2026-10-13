<?php
$leadFeedback = $leadFeedback ?? null;
$csrfToken = $csrfToken ?? '';
$campaigns = $campaigns ?? [];
$advisors = $advisors ?? [];
?>
<!-- CRM Kanban -->
        <div id="crmView">
          <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
              <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Gestión de Leads</h1>
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Registra prospectos, guarda sus datos de contacto y conviértelos en clientes cuando estén validados.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
              <div class="relative sm:w-72">
                <label for="leadSearch" class="sr-only">Buscar leads</label>
                <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                <input id="leadSearch" type="search" placeholder="Buscar por nombre, estado o campaña" autocomplete="off" class="h-10 w-full rounded-lg border border-gray-200 bg-white pl-9 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
              </div>
              <button id="newLeadBtn" type="button" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-500/30">
                <i data-lucide="plus" class="h-4 w-4"></i>Nuevo Lead
              </button>
            </div>
          </div>

          <p id="leadFeedback" role="status" aria-live="polite" class="mb-4 rounded-lg border px-3 py-2 text-sm <?= $leadFeedback !== null ? '' : 'hidden' ?>"><?= $leadFeedback !== null ? htmlspecialchars($leadFeedback, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '' ?></p>

          <div class="-mx-4 overflow-x-auto px-4 pb-4 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div id="kanbanBoard" class="flex min-w-max gap-4" role="list" aria-label="Embudo comercial">
              <?php foreach ($leads as $lead): ?>
                <?php
                $leadId = (int) ($lead['id'] ?? 0);
                $leadClientId = $lead['client_id'] ?? null;
                ?>
                <article class="w-80 shrink-0 self-start rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900" role="listitem">
                  <div class="flex items-start justify-between gap-3">
                    <div>
                      <h2 class="font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars((string) ($lead['name'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h2>
                      <p class="mt-1 text-sm text-slate-500 dark:text-slate-400"><?= htmlspecialchars((string) ($lead['status'] ?? 'Nuevo Lead'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                    </div>
                    <?php if ($leadClientId !== null): ?>
                      <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">Cliente</span>
                    <?php endif; ?>
                  </div>
                  <?php if (($lead['project'] ?? '') !== '' || ($lead['advisor'] ?? '') !== ''): ?>
                    <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                      <?= htmlspecialchars((string) ($lead['project'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                      <?= ($lead['project'] ?? '') !== '' && ($lead['advisor'] ?? '') !== '' ? ' · ' : '' ?>
                      <?= htmlspecialchars((string) ($lead['advisor'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                    </p>
                  <?php endif; ?>
                  <?php if ($leadClientId === null): ?>
                    <details class="mt-4 border-t border-gray-100 pt-3 dark:border-slate-800">
                      <summary class="cursor-pointer text-sm font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">Convertir a cliente</summary>
                      <form method="post" action="?view=crm" class="mt-3 space-y-3">
                        <input type="hidden" name="action" value="convert_to_client">
                        <input type="hidden" name="id" value="<?= $leadId ?>">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                        <div>
                          <label for="lead-dni-<?= $leadId ?>" class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-200">DNI o documento <span class="text-rose-500">*</span></label>
                          <input id="lead-dni-<?= $leadId ?>" name="dni" type="text" maxlength="20" required class="h-9 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        </div>
                        <div>
                          <label for="lead-phone-<?= $leadId ?>" class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-200">Teléfono</label>
                          <input id="lead-phone-<?= $leadId ?>" name="phone" type="tel" maxlength="30" value="<?= htmlspecialchars((string) ($lead['phone'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" class="h-9 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        </div>
                        <div>
                          <label for="lead-email-<?= $leadId ?>" class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-200">Correo electrónico</label>
                          <input id="lead-email-<?= $leadId ?>" name="email" type="email" maxlength="120" value="<?= htmlspecialchars((string) ($lead['email'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" class="h-9 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-brand-600 px-3 py-2 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-500/30">Crear ficha de cliente</button>
                      </form>
                    </details>
                  <?php endif; ?>
                </article>
              <?php endforeach; ?>
              <?php if ($leads === []): ?>
                <p class="rounded-xl border border-dashed border-gray-300 px-6 py-8 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">No hay leads registrados.</p>
              <?php endif; ?>
            </div>
          </div>

          <div id="leadModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="leadModalTitle">
            <button type="button" data-lead-close class="absolute inset-0 h-full w-full cursor-default bg-slate-950/60 backdrop-blur-sm" aria-label="Cerrar formulario"></button>
            <div class="relative flex min-h-full items-center justify-center p-4">
              <form id="leadForm" method="post" action="?view=crm" class="relative w-full max-w-2xl space-y-5 rounded-xl border border-gray-200 bg-white p-6 shadow-2xl dark:border-slate-700 dark:bg-slate-900">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                <div class="flex items-start justify-between gap-4">
                  <div>
                    <h2 id="leadModalTitle" class="text-lg font-semibold text-slate-900 dark:text-white">Nuevo Lead</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Registra los datos disponibles.</p>
                  </div>
                  <button type="button" data-lead-close aria-label="Cerrar" class="rounded-lg p-2 text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800"><i data-lucide="x" class="h-4 w-4"></i></button>
                </div>
                <div>
                  <label for="leadName" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Nombre (obligatorio)</label>
                  <input id="leadName" name="name" type="text" maxlength="100" required autocomplete="name" class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                </div>
                <div>
                  <label for="leadPhone" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Teléfono</label>
                  <input id="leadPhone" name="phone" type="tel" maxlength="30" autocomplete="tel" placeholder="+51 999 999 999" class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                </div>
                <div>
                  <label for="leadEmail" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Correo electrónico</label>
                  <input id="leadEmail" name="email" type="email" maxlength="120" autocomplete="email" placeholder="cliente@correo.com" class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                </div>
                <div>
                  <label for="leadProject" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Proyecto de interés</label>
                  <input id="leadProject" name="project" type="text" maxlength="120" placeholder="Proyecto o inmueble" class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                </div>
                <div>
                  <label for="leadInterest" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Nivel de interés</label>
                  <select id="leadInterest" name="interest" class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="Bajo">Bajo</option><option value="Medio">Medio</option><option value="Alto">Alto</option><option value="Urgente">Urgente</option>
                  </select>
                </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                  <div>
                    <label for="leadCampaign" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Campaña</label>
                    <select id="leadCampaign" name="campaign_id" class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                      <option value="">Sin campaña</option>
                      <?php foreach ($campaigns as $campaign): ?>
                        <option value="<?= (int) $campaign['id'] ?>"><?= htmlspecialchars($campaign['nombre'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div>
                    <label for="leadAdvisor" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Asesor</label>
                    <select id="leadAdvisor" name="advisor_id" class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                      <option value="">Sin asesor</option>
                      <?php foreach ($advisors as $advisor): ?>
                        <option value="<?= (int) $advisor['id'] ?>"><?= htmlspecialchars($advisor['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-gray-100 pt-4 dark:border-slate-800">
                  <button type="button" data-lead-close class="h-10 rounded-lg border border-gray-200 px-4 text-sm font-medium text-slate-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Cancelar</button>
                  <button type="submit" class="inline-flex h-10 items-center gap-2 rounded-lg bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-500/30"><i data-lucide="save" class="h-4 w-4"></i>Guardar Lead</button>
                </div>
              </form>
            </div>
          </div>
        </div>