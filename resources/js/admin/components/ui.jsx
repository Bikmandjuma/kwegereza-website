import StarMark from './StarMark'

export function Button({ variant = 'primary', size = 'md', className = '', ...props }) {
  const base =
    'inline-flex items-center justify-center gap-2 rounded-xl font-medium transition-all duration-200 ease-spring ' +
    'disabled:opacity-50 disabled:pointer-events-none disabled:shadow-none ' +
    'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-teal-950 ' +
    'active:scale-[0.97]'
  const sizes = {
    sm: 'px-3 py-1.5 text-xs',
    md: 'px-4 py-2.5 text-sm',
    lg: 'px-5 py-3 text-sm',
  }
  const variants = {
    primary:
      'bg-gradient-to-br from-teal-700 to-teal-900 text-white shadow-soft hover:shadow-lift hover:-translate-y-0.5',
    gold:
      'bg-gradient-to-br from-gold-500 to-gold-700 text-white shadow-soft hover:shadow-glow hover:-translate-y-0.5',
    secondary:
      'bg-white text-teal-900 border border-sand-200 hover:bg-sand-100 hover:-translate-y-0.5 ' +
      'dark:bg-teal-900/60 dark:text-sand-50 dark:border-white/10 dark:hover:bg-teal-800/60',
    danger:
      'bg-gradient-to-br from-rose-600 to-rose-700 text-white shadow-soft hover:shadow-lift hover:-translate-y-0.5',
    ghost:
      'text-teal-900 hover:bg-sand-100 dark:text-sand-100 dark:hover:bg-white/10',
  }
  return <button className={`${base} ${sizes[size]} ${variants[variant]} ${className}`} {...props} />
}

export function Input({ label, error, hint, className = '', id, ...props }) {
  return (
    <label className="block">
      {label && (
        <span className="mb-1.5 block text-sm font-medium text-ink dark:text-sand-100">{label}</span>
      )}
      <input
        id={id}
        className={`w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-ink outline-none transition-all duration-150 placeholder:text-ink/35 ${
          error
            ? 'border-rose-600 focus:border-rose-600 focus-visible:ring-2 focus-visible:ring-rose-600/25'
            : 'border-sand-200 focus:border-teal-600 focus-visible:ring-2 focus-visible:ring-teal-600/25'
        } dark:bg-teal-900/40 dark:text-sand-50 dark:placeholder:text-sand-100/30 dark:border-white/10 dark:focus:border-gold-400 ${className}`}
        {...props}
      />
      {hint && !error && <span className="mt-1 block text-xs text-ink/45 dark:text-sand-100/45">{hint}</span>}
      {error && <span className="mt-1 block text-xs text-rose-600 dark:text-rose-400">{error}</span>}
    </label>
  )
}

export function Card({ className = '', glass = false, hover = false, children }) {
  return (
    <div
      className={`rounded-xl2 border transition-all duration-200 ease-spring ${
        glass
          ? 'glass'
          : 'border-sand-200 bg-white shadow-soft dark:border-white/10 dark:bg-teal-900/40'
      } ${hover ? 'hover:-translate-y-0.5 hover:shadow-lift' : ''} ${className}`}
    >
      {children}
    </div>
  )
}

/**
 * Was reimplementing the same rotated-squares shape as StarMark from
 * scratch, as a second, slightly-diverging copy of what's meant to be
 * the app's one signature mark (the 8-point khatam star — see
 * StarMark.jsx's own comment: "the one signature mark... a faint
 * watermark on... empty states"). Now actually reuses it, so the two
 * can't quietly drift apart from each other.
 */
export function EmptyState({ title, description, action }) {
  return (
    <div className="flex flex-col items-center justify-center gap-3 rounded-xl2 border border-dashed border-sand-200 bg-white/60 px-6 py-16 text-center dark:border-white/10 dark:bg-teal-900/20">
      <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-teal-100 to-sand-100 dark:from-teal-800/50 dark:to-teal-900/50">
        <StarMark className="h-8 w-8 text-teal-700/50 dark:text-sand-100/40" />
      </div>
      <h3 className="font-display text-lg text-ink dark:text-sand-50">{title}</h3>
      {description && <p className="max-w-sm text-sm text-ink/60 dark:text-sand-100/60">{description}</p>}
      {action}
    </div>
  )
}

export function TableSkeleton({ rows = 5, cols = 4 }) {
  return (
    <div className="divide-y divide-sand-200 dark:divide-white/10">
      {Array.from({ length: rows }).map((_, r) => (
        <div key={r} className="flex gap-4 px-4 py-3">
          {Array.from({ length: cols }).map((__, c) => (
            <div key={c} className="shimmer h-4 flex-1 animate-shimmer rounded" style={{ animationDelay: `${r * 60}ms` }} />
          ))}
        </div>
      ))}
    </div>
  )
}

/**
 * Generic block-shaped skeleton, complementing TableSkeleton (which only
 * fits tabular layouts). Several pages built earlier this project fell
 * back to a plain "Turapakira..." text line for non-table loading states
 * (Dashboard cards, chat panes, forms) — inconsistent with the skeleton
 * treatment used elsewhere. This gives those pages a matching option.
 */
export function Skeleton({ className = '' }) {
  return <div className={`shimmer animate-shimmer rounded-lg ${className}`} />
}

export function CardSkeleton({ lines = 3 }) {
  return (
    <div className="space-y-3">
      <div className="shimmer h-3 w-1/3 animate-shimmer rounded" />
      {Array.from({ length: lines }).map((_, i) => (
        <div key={i} className="shimmer h-3 animate-shimmer rounded" style={{ width: `${85 - i * 12}%` }} />
      ))}
    </div>
  )
}

export function Badge({ tone = 'neutral', children }) {
  const tones = {
    neutral: 'bg-sand-100 text-ink/70 dark:bg-white/10 dark:text-sand-100/80',
    success: 'bg-teal-100 text-teal-900 dark:bg-teal-800/50 dark:text-teal-100',
    warning: 'bg-gold-300/40 text-gold-700 dark:bg-gold-500/20 dark:text-gold-300',
    danger: 'bg-rose-100 text-rose-600 dark:bg-rose-700/20 dark:text-rose-300',
  }
  return (
    <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${tones[tone]}`}>
      {children}
    </span>
  )
}

/**
 * "X items" shown at the TOP of a table/list, next to its heading —
 * distinct from the pagination footer's "page X of Y (Z total)" text,
 * which is easy to miss until you've scrolled past the whole table.
 * Usage: <ResultCount count={meta.total} label="abanyeshuri" />
 */
export function ResultCount({ count, label }) {
  if (count === null || count === undefined) {
    return <Skeleton className="h-5 w-24" />
  }
  return (
    <span className="inline-flex items-center rounded-full bg-sand-100 px-2.5 py-1 text-xs font-medium text-ink/60 dark:bg-white/10 dark:text-sand-100/60">
      {count} {label}
    </span>
  )
}

/**
 * Shared Prev/Next pagination footer — every paginated list in the app
 * used to hand-roll this same six lines; one component now so behavior
 * (disabled-at-bounds, wording) can't drift between pages.
 * Usage: <Pagination meta={state.meta} page={page} onChange={setPage} />
 */
export function Pagination({ meta, page, onChange }) {
  if (!meta || meta.last_page <= 1) return null
  return (
    <div className="flex items-center justify-between border-t border-sand-200 px-4 py-3 text-sm dark:border-white/10">
      <span className="text-ink/50 dark:text-sand-100/50">
        Impapuro {meta.current_page} kuri {meta.last_page} ({meta.total} byose)
      </span>
      <div className="flex gap-2">
        <Button variant="secondary" size="sm" disabled={meta.current_page <= 1} onClick={() => onChange(page - 1)}>
          ‹ Ibanziriza
        </Button>
        <Button variant="secondary" size="sm" disabled={meta.current_page >= meta.last_page} onClick={() => onChange(page + 1)}>
          Gukurikira ›
        </Button>
      </div>
    </div>
  )
}
