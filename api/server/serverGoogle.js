const express = require('express');
const session = require('express-session');
const passport = require('passport');
const GoogleStrategy = require('passport-google-oauth20').Strategy;
const credentials = require('./credenciales.json');

const app = express();

//Configurar sesión
app.use(session({
  secret: 'un_secreto_para_sesiones',
  resave: false,
  saveUninitialized: true,
}));

app.use(passport.initialize());
app.use(passport.session());

//Serializar y deserializar usuario
passport.serializeUser((user, done) => done(null, user));
passport.deserializeUser((user, done) => done(null, user));

passport.use(new GoogleStrategy({
    clientID: credentials.web.client_id,
    clientSecret: credentials.web.client_secret,
    callbackURL: credentials.web.redirect_uris[0]
  },
  (accessToken, refreshToken, profile, done) => {
    return done(null, profile);
  }
));

app.get('/auth/google', passport.authenticate('google', { scope: ['profile', 'email'] }));

app.get('/auth/google/callback',
  passport.authenticate('google', { failureRedirect: '/' }),
  (req, res) => {
    const nombreCompleto = req.user.displayName ? req.user.displayName.split(' ') : [];
    const nombreCliente = nombreCompleto[0] || '';
    const apellidoPaterno = nombreCompleto[1] || '';
    const apellidoMaterno = nombreCompleto[2] || '';
    const correoElectronico = req.user.emails?.[0]?.value || '';
    const fotoPerfil = req.user.photos?.[0]?.value || req.user._json.picture || '';

    const contraseña = "123456";

    const params = new URLSearchParams({
      nombreCliente,
      apellidoPaterno,
      apellidoMaterno,
      correoElectronico,
      contraseña,
      fotoPerfil
    }).toString();

    res.redirect(`http://localhost:8000/backEnd/servicios/loginGoogle.php?${params}`);
  }
);

app.get('/perfil', (req, res) => {
  if (!req.user) return res.send('No autenticado');

  const nombreCompleto = req.user.displayName ? req.user.displayName.split(' ') : [];
  const usuario = {
    nombreCliente: nombreCompleto[0] || '',
    apellidoPaterno: nombreCompleto[1] || '',
    apellidoMaterno: nombreCompleto[2] || '',
    correoElectronico: req.user.emails?.[0]?.value || '',
    fotoPerfil: req.user.photos?.[0]?.value || '',
    idGoogle: req.user.id
  };

  res.json(usuario);
});

app.listen(3000, () => console.log('Servidor Node escuchando en http://localhost:3000'));
