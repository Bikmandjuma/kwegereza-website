import { ArrowDown, ArrowUp, ArrowUpDown, ChevronLeft, ChevronRight, Download, Search } from "lucide-react";

const ROW_PER_PAGE_OPTIONS = [10, 20, 50, 100];

/**
 * Generic table that implements every element the spec's "TABLE STANDARD"
 * requires: title, total count, search, filtering (via the `filters` slot),
 * sorting, pagination, rows-per-page, loading/empty/error states, responsive
 * mobile behavior (columns can be hidden below a breakpoint), bulk actions,
 * and export.
 *
 * The table itself is presentation-only — it doesn't fetch data or own
 * pagination state. The page using it (e.g. UserManagementPage) owns state
 * and passes callbacks, so this component can be reused for any list without
 * knowing anything about users/students/ifaida/etc.
 *
 * columns: [{ key, label, sortable?, hideBelow?: "sm"|"md", render(row) }]
 */
export default function DataTable({
  title,
  itemLabel = "ibintu",
  total = 0,
  columns,
  rows,
  rowKey = (row) => row.id,
  loading = false,
  error = "",
  emptyMessage = "Nta kintu kiboneka.",
  search,
  filters,
  sort,
  onSortChange,
  page = 1,
  perPage = 20,
  totalPages = 1,
  onPageChange,
  onPerPageChange,
  selectable = false,
  selectedIds,
  onToggleRow,
  onToggleAll,
  bulkActions = [],
  onExport,
  renderRowActions,
}) {
  const hideClass = { sm: "hidden sm:table-cell", md: "hidden md:table-cell" };
  const allSelected = selectable && rows.length > 0 && rows.every((r) => selectedIds?.has(rowKey(r)));
  const anySelected = selectable && selectedIds?.size > 0;

  return (
    <div>
      {/* Title, count, search, filters, sort, export */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
        <div>
          {title && <h2 className="font-display text-lg font-bold text-green-950">{title}</h2>}
          <p className="text-ink-soft text-sm font-semibold">
            {total.toLocaleString()} {itemLabel}
          </p>
        </div>
        <div className="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
          {search && (
            <div className="flex items-center gap-2 bg-surface border border-line rounded-full px-4 py-2.5 w-full sm:w-[260px]">
              <Search size={16} className="text-ink-soft flex-none" />
              <input
                value={search.value}
                onChange={(e) => search.onChange(e.target.value)}
                placeholder={search.placeholder ?? "Shakisha..."}
                className="w-full text-sm outline-none bg-transparent"
              />
            </div>
          )}
          {filters}
          {onExport && (
            <button
              onClick={onExport}
              className="btn btn-outline !border-line !text-green-950 !py-2.5 text-xs whitespace-nowrap"
            >
              <Download size={14} /> Kohereza (CSV)
            </button>
          )}
        </div>
      </div>

      {/* Bulk actions bar — only shown once something is selected */}
      {selectable && anySelected && (
        <div className="flex items-center justify-between bg-cream-2 border border-line rounded-xl px-4 py-2.5 mb-3">
          <span className="text-xs font-bold text-green-950">{selectedIds.size} byatoranyijwe</span>
          <div className="flex gap-2">
            {bulkActions.map((action) => (
              <button
                key={action.label}
                onClick={() => action.onClick(Array.from(selectedIds))}
                className={`text-xs font-bold px-3 py-1.5 rounded-lg flex items-center gap-1.5 ${
                  action.danger ? "bg-red-600 text-white" : "bg-green-950 text-white"
                }`}
              >
                {action.icon}
                {action.label}
              </button>
            ))}
          </div>
        </div>
      )}

      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-4">{error}</div>
      )}

      <div className="card !p-0 overflow-hidden">
        {loading ? (
          <div className="p-10 text-center text-ink-soft text-sm">Turimo gupakira...</div>
        ) : rows.length === 0 ? (
          <div className="p-10 text-center text-ink-soft text-sm">{emptyMessage}</div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-cream-2 text-ink-soft text-[11px] uppercase font-bold tracking-wide">
                <tr>
                  {selectable && (
                    <th className="px-5 py-3 w-10">
                      <input
                        type="checkbox"
                        checked={allSelected}
                        onChange={() => onToggleAll?.(rows.map(rowKey))}
                        className="w-4 h-4"
                      />
                    </th>
                  )}
                  {columns.map((col) => (
                    <th
                      key={col.key}
                      className={`text-left px-5 py-3 ${hideClass[col.hideBelow] ?? ""} ${
                        col.align === "right" ? "text-right" : ""
                      }`}
                    >
                      {col.sortable ? (
                        <button
                          onClick={() => onSortChange?.(col.key)}
                          className="flex items-center gap-1 hover:text-green-950"
                        >
                          {col.label}
                          {sort?.key === col.key ? (
                            sort.order === "asc" ? (
                              <ArrowUp size={12} />
                            ) : (
                              <ArrowDown size={12} />
                            )
                          ) : (
                            <ArrowUpDown size={11} className="opacity-40" />
                          )}
                        </button>
                      ) : (
                        col.label
                      )}
                    </th>
                  ))}
                  {renderRowActions && <th className="px-5 py-3 text-right">Ibikorwa</th>}
                </tr>
              </thead>
              <tbody>
                {rows.map((row) => {
                  const key = rowKey(row);
                  return (
                    <tr key={key} className="border-t border-line hover:bg-cream-2/40">
                      {selectable && (
                        <td className="px-5 py-4">
                          <input
                            type="checkbox"
                            checked={selectedIds?.has(key) ?? false}
                            onChange={() => onToggleRow?.(key)}
                            className="w-4 h-4"
                          />
                        </td>
                      )}
                      {columns.map((col) => (
                        <td
                          key={col.key}
                          className={`px-5 py-4 ${hideClass[col.hideBelow] ?? ""} ${
                            col.align === "right" ? "text-right" : ""
                          }`}
                        >
                          {col.render ? col.render(row) : row[col.key]}
                        </td>
                      ))}
                      {renderRowActions && <td className="px-5 py-4 text-right">{renderRowActions(row)}</td>}
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {/* Pagination + rows-per-page */}
      {!loading && rows.length > 0 && (onPageChange || onPerPageChange) && (
        <div className="flex flex-col sm:flex-row items-center justify-between gap-3 mt-4 text-xs text-ink-soft">
          {onPerPageChange && (
            <div className="flex items-center gap-2">
              <span>Imirongo kuri paji:</span>
              <select
                value={perPage}
                onChange={(e) => onPerPageChange(Number(e.target.value))}
                className="border border-line rounded-lg px-2 py-1 bg-surface"
              >
                {ROW_PER_PAGE_OPTIONS.map((n) => (
                  <option key={n} value={n}>
                    {n}
                  </option>
                ))}
              </select>
            </div>
          )}
          {onPageChange && (
            <div className="flex items-center gap-2">
              <button
                onClick={() => onPageChange(Math.max(1, page - 1))}
                disabled={page <= 1}
                className="w-8 h-8 rounded-lg border border-line flex items-center justify-center disabled:opacity-40"
              >
                <ChevronLeft size={14} />
              </button>
              <span className="font-semibold">
                Paji {page} / {totalPages}
              </span>
              <button
                onClick={() => onPageChange(Math.min(totalPages, page + 1))}
                disabled={page >= totalPages}
                className="w-8 h-8 rounded-lg border border-line flex items-center justify-center disabled:opacity-40"
              >
                <ChevronRight size={14} />
              </button>
            </div>
          )}
        </div>
      )}
    </div>
  );
}

/** Client-side CSV builder shared by every page that wires up DataTable's export button. */
export function downloadCsv(filename, columns, rows) {
  const escape = (val) => `"${String(val ?? "").replace(/"/g, '""')}"`;
  const header = columns.map((c) => escape(c.label)).join(",");
  const body = rows.map((row) => columns.map((c) => escape(c.csvValue ? c.csvValue(row) : row[c.key])).join(",")).join("\n");
  const blob = new Blob([`${header}\n${body}`], { type: "text/csv;charset=utf-8;" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = filename;
  a.click();
  URL.revokeObjectURL(url);
}
