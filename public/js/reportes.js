(function () {
  const exportButtons = document.querySelectorAll('.report-export-btn');
  if (!exportButtons.length) return;

  const escapeCsv = (value) => {
    const text = value == null ? '' : String(value);
    return /[",\n]/.test(text) ? `"${text.replace(/"/g, '""')}"` : text;
  };

  const downloadCsv = (filename, rows) => {
    const csv = rows.map((row) => row.map(escapeCsv).join(',')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    link.remove();
    URL.revokeObjectURL(link.href);
  };

  exportButtons.forEach((button) => {
    button.addEventListener('click', () => {
      const type = button.dataset.export;

      if (type === 'ventas') {
        const rows = [
          ['Mes', 'Total de ventas'],
          ...((Array.isArray(reportesVentasMes) ? reportesVentasMes : []).map((row) => [row.mes ?? 'Mes', row.total_ventas ?? 0])),
        ];
        downloadCsv('ventas_por_mes.csv', rows);
        return;
      }

      if (type === 'inventario') {
        const rows = [
          ['Estado', 'Cantidad'],
          ...((Array.isArray(reportesInventario) ? reportesInventario : []).map((row) => [row.estado ?? 'Sin estado', row.total ?? 0])),
        ];
        downloadCsv('inventario_por_estado.csv', rows);
      }
    });
  });
})();
