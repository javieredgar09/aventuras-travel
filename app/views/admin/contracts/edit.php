<!-- admin/contracts/edit.php -->
<div class="mb-4 flex items-center gap-3 bg-white p-3 rounded-xl border border-petroleo/5 shadow-sm">
    <a href="<?= Router::url('/admin/contracts/' . $contrato['id']) ?>" class="w-8 h-8 rounded-full bg-superficie text-petroleo flex items-center justify-center hover:bg-turquesa hover:text-white transition-colors">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
    </a>
    <div>
        <h1 class="text-xl text-petroleo font-black leading-none">Editar Contrato <?= htmlspecialchars($contrato['codigo']) ?></h1>
        <p class="text-xs text-petroleo/60 mt-1">Contrato asociado al grupo ID <?= htmlspecialchars($contrato['grupo_id']) ?></p>
    </div>
</div>

<form action="<?= Router::url('/admin/contracts/' . $contrato['id'] . '/update') ?>" method="POST" class="bg-white p-6 rounded-xl border border-petroleo/5 shadow-sm">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-petroleo mb-1">Código</label>
            <input type="text" name="codigo" value="<?= htmlspecialchars($contrato['codigo']) ?>" class="w-full px-3 py-2 rounded border">
        </div>
        <div>
            <label class="block text-xs font-bold text-petroleo mb-1">Destino</label>
            <input type="text" name="destino" value="<?= htmlspecialchars($contrato['destino']) ?>" class="w-full px-3 py-2 rounded border">
        </div>
        <div>
            <label class="block text-xs font-bold text-petroleo mb-1">Fecha Salida</label>
            <input type="date" name="fecha_salida" value="<?= htmlspecialchars($contrato['fecha_salida']) ?>" class="w-full px-3 py-2 rounded border">
        </div>
        <div>
            <label class="block text-xs font-bold text-petroleo mb-1">Fecha Retorno</label>
            <input type="date" name="fecha_retorno" value="<?= htmlspecialchars($contrato['fecha_retorno']) ?>" class="w-full px-3 py-2 rounded border">
        </div>
        <div>
            <label class="block text-xs font-bold text-petroleo mb-1">Valor Total</label>
            <input type="number" step="0.01" name="valor_total" value="<?= htmlspecialchars($contrato['valor_total']) ?>" class="w-full px-3 py-2 rounded border">
        </div>
        <div>
            <label class="block text-xs font-bold text-petroleo mb-1">Depósito</label>
            <input type="number" step="0.01" name="deposito" value="<?= htmlspecialchars($contrato['deposito']) ?>" class="w-full px-3 py-2 rounded border">
        </div>
        <div>
            <label class="block text-xs font-bold text-petroleo mb-1">Estado</label>
            <select name="estado" class="w-full px-3 py-2 rounded border">
                <option value="activo" <?= $contrato['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                <option value="completado" <?= $contrato['estado'] === 'completado' ? 'selected' : '' ?>>Completado</option>
                <option value="cancelado" <?= $contrato['estado'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
            </select>
        </div>
    </div>

    <div class="mt-4 text-right">
        <a href="<?= Router::url('/admin/contracts/' . $contrato['id']) ?>" class="px-4 py-2 mr-2 rounded border">Cancelar</a>
        <button type="submit" class="px-6 py-2 rounded bg-turquesa text-white font-bold">Guardar cambios</button>
    </div>
</form>
