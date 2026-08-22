import { Link } from "react-router-dom";
import SiteStatsBar from "./SiteStatsBar.jsx";

export default function Footer() {
  return (
    <footer className="bg-green-950 text-[#cfe0d6] mt-20 pt-16">
      <div className="max-w-[1240px] mx-auto px-7">
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-10 pb-11 border-b border-white/10">
          <div>
            <div className="flex items-center gap-3 mb-4">
              <div className="w-[46px] h-[46px] rounded-full border-2 border-gold-500 bg-green-950 flex items-center justify-center flex-none">
                <svg viewBox="0 0 24 24" fill="none" stroke="#cf9d3f" strokeWidth="1.6" className="w-6 h-6">
                  <path d="M4 21V11l8-6 8 6v10" />
                  <path d="M9 21v-6h6v6" />
                </svg>
              </div>
              <div className="leading-tight">
                <b className="text-white block">K.I.U</b>
                <small className="text-[#a9c1b3] block">Kwegereza Islam Umuryango</small>
              </div>
            </div>
            <p className="text-[13.5px] text-[#a9c1b3] leading-relaxed max-w-[280px]">
              Urubuga rwigisha ubumenyi bw'idini ya Islamu bushingiye kuri Qur'an na Sunnah.
            </p>
          </div>
          <div>
            <h4 className="text-white text-sm mb-4">Aho winjira</h4>
            <Link to="/" className="block text-[13.5px] mb-2.5 hover:text-gold-400">Home</Link>
            <Link to="/about" className="block text-[13.5px] mb-2.5 hover:text-gold-400">About Us</Link>
            <Link to="/abarimu" className="block text-[13.5px] mb-2.5 hover:text-gold-400">Abarimu</Link>
            <Link to="/inyandiko" className="block text-[13.5px] mb-2.5 hover:text-gold-400">Inyandiko</Link>
          </div>
          <div>
            <h4 className="text-white text-sm mb-4">Ibindi</h4>
            <Link to="/amatangazo" className="block text-[13.5px] mb-2.5 hover:text-gold-400">Amatangazo</Link>
            <Link to="/ibitabo" className="block text-[13.5px] mb-2.5 hover:text-gold-400">Ibitabo</Link>
            <a href="#" className="block text-[13.5px] mb-2.5 hover:text-gold-400">Twiyungeho</a>
            <a href="#" className="block text-[13.5px] mb-2.5 hover:text-gold-400">Injira</a>
          </div>
          <div>
            <h4 className="text-white text-sm mb-4">Twandikire</h4>
            <a href="tel:+250723061482" className="block text-[13.5px] mb-2.5 hover:text-gold-400">(+250) 723 061 482</a>
            <a href="mailto:umuryangok@gmail.com" className="block text-[13.5px] mb-2.5 hover:text-gold-400">umuryangok@gmail.com</a>
            <span className="block text-[13.5px] mb-2.5">Kigali, Rwanda</span>
          </div>
        </div>
        <SiteStatsBar />
        <div className="flex flex-col sm:flex-row gap-2 justify-between items-center py-5 text-[12.5px] text-[#9db4a7]">
          <span>© 2026 Kwegereza Islam Umuryango. Uburenganzira bwose burafitwe.</span>
          <span>Yakozwe n'urukundo rwo kwigisha</span>
        </div>
      </div>
    </footer>
  );
}
