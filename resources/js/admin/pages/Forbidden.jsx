import { Link } from 'react-router-dom'
import StarMark from '../components/StarMark'

export default function Forbidden() {
  return (
    <div className="flex h-full flex-col items-center justify-center gap-3 py-24 text-center">
      <StarMark className="h-10 w-10 text-rose-600/40" />
      <h1 className="font-display text-xl text-ink">Ntabwo ufite uburenganzira</h1>
      <p className="text-sm text-ink/60">Ntushobora kubona iyi paji.</p>
      <Link to="/dashboard" className="text-sm text-teal-700 hover:underline">Subira Ahabanza</Link>
    </div>
  )
}
