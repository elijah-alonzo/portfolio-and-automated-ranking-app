import Footer from './Footer';
import Navigation from './Navigation';

export default function PublicLayout({ children }) {
    return (
        <>
            <Navigation />
            <main>{children}</main>
            <Footer />
        </>
    );
}
