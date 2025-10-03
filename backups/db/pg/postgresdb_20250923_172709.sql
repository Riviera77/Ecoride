--
-- PostgreSQL database dump
--

\restrict Y9TYYUmtPHco8wZNw0tsXuNPzrZgF0cetZ7b7aJa7KwqE1XorRIE4K8mdHvpaeC

-- Dumped from database version 16.10
-- Dumped by pg_dump version 16.10

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: notify_messenger_messages(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.notify_messenger_messages() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
        BEGIN
            PERFORM pg_notify('messenger_messages', NEW.queue_name::text);
            RETURN NEW;
        END;
    $$;


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: car; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.car (
    id integer NOT NULL,
    users_id integer,
    registration character varying(50) NOT NULL,
    date_first_registration character varying(50) NOT NULL,
    model character varying(50) NOT NULL,
    color character varying(50) NOT NULL,
    mark character varying(50) NOT NULL,
    energy boolean NOT NULL
);


--
-- Name: car_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.car_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: car_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.car_id_seq OWNED BY public.car.id;


--
-- Name: carpooling; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.carpooling (
    id integer NOT NULL,
    cars_id integer NOT NULL,
    departure_address character varying(50) NOT NULL,
    arrival_address character varying(50) NOT NULL,
    departure_date date NOT NULL,
    arrival_date date NOT NULL,
    departure_time time(0) without time zone NOT NULL,
    arrival_time time(0) without time zone NOT NULL,
    price double precision NOT NULL,
    number_seats integer NOT NULL,
    preference character varying(50) DEFAULT NULL::character varying,
    status character varying(50) NOT NULL,
    users_id integer NOT NULL,
    validated_passenger_ids json,
    problem_reports json
);


--
-- Name: carpooling_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.carpooling_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: carpooling_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.carpooling_id_seq OWNED BY public.carpooling.id;


--
-- Name: carpooling_passengers; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.carpooling_passengers (
    carpooling_id integer NOT NULL,
    user_id integer NOT NULL
);


--
-- Name: credit; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.credit (
    id integer NOT NULL,
    users_id integer,
    balance integer NOT NULL,
    transaction_date timestamp(0) without time zone NOT NULL
);


--
-- Name: credit_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.credit_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: credit_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.credit_id_seq OWNED BY public.credit.id;


--
-- Name: doctrine_migration_versions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.doctrine_migration_versions (
    version character varying(191) NOT NULL,
    executed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    execution_time integer
);


--
-- Name: messenger_messages; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.messenger_messages (
    id bigint NOT NULL,
    body text NOT NULL,
    headers text NOT NULL,
    queue_name character varying(190) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    available_at timestamp(0) without time zone NOT NULL,
    delivered_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


--
-- Name: COLUMN messenger_messages.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.messenger_messages.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN messenger_messages.available_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.messenger_messages.available_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN messenger_messages.delivered_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.messenger_messages.delivered_at IS '(DC2Type:datetime_immutable)';


--
-- Name: messenger_messages_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.messenger_messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: messenger_messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.messenger_messages_id_seq OWNED BY public.messenger_messages.id;


--
-- Name: user; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public."user" (
    id integer NOT NULL,
    email character varying(180) NOT NULL,
    roles json NOT NULL,
    password character varying(255) NOT NULL,
    username character varying(50) NOT NULL,
    photo character varying(50),
    role_type json,
    preference text
);


--
-- Name: user_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_id_seq OWNED BY public."user".id;


--
-- Name: car id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.car ALTER COLUMN id SET DEFAULT nextval('public.car_id_seq'::regclass);


--
-- Name: carpooling id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.carpooling ALTER COLUMN id SET DEFAULT nextval('public.carpooling_id_seq'::regclass);


--
-- Name: credit id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.credit ALTER COLUMN id SET DEFAULT nextval('public.credit_id_seq'::regclass);


--
-- Name: messenger_messages id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.messenger_messages ALTER COLUMN id SET DEFAULT nextval('public.messenger_messages_id_seq'::regclass);


--
-- Name: user id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public."user" ALTER COLUMN id SET DEFAULT nextval('public.user_id_seq'::regclass);


--
-- Data for Name: car; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.car (id, users_id, registration, date_first_registration, model, color, mark, energy) FROM stdin;
13	18	1234ABCD	2000-01-01	Clio	Bleu	Renault	f
14	19	F58746CD	2012-07-15	PROACE	noir	Toyota	t
15	20	D956GR56	2022-01-01	E-208	gris	peugeot	t
\.


--
-- Data for Name: carpooling; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.carpooling (id, cars_id, departure_address, arrival_address, departure_date, arrival_date, departure_time, arrival_time, price, number_seats, preference, status, users_id, validated_passenger_ids, problem_reports) FROM stdin;
13	13	Paris	Lyon	2025-12-01	2025-12-01	08:00:00	13:00:00	50	3	non fumeur	ouvert	18	\N	\N
14	14	Paris	Rennes	2025-12-01	2025-12-01	08:30:00	12:00:00	30	2	animaux acceptés	fermé	19	\N	\N
15	15	Paris	Marseille	2026-01-01	2026-01-01	08:30:00	15:30:00	80	2	non fumeurs, animaux acceptés	ouvert	20	\N	\N
\.


--
-- Data for Name: carpooling_passengers; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.carpooling_passengers (carpooling_id, user_id) FROM stdin;
13	19
14	20
14	18
15	18
\.


--
-- Data for Name: credit; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.credit (id, users_id, balance, transaction_date) FROM stdin;
12	\N	20	2025-11-01 00:00:00
13	\N	16	2025-11-01 00:00:00
14	\N	10	2025-11-01 00:00:00
15	23	20	2025-05-18 15:49:43
16	24	20	2025-05-18 15:53:29
\.


--
-- Data for Name: doctrine_migration_versions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.doctrine_migration_versions (version, executed_at, execution_time) FROM stdin;
DoctrineMigrations\\Version20250330163221	2025-04-27 20:43:40	53
DoctrineMigrations\\Version20250421125617	2025-04-27 20:43:40	9
DoctrineMigrations\\Version20250421180649	2025-04-27 20:43:40	4
DoctrineMigrations\\Version20250424223400	2025-04-27 20:43:40	0
DoctrineMigrations\\Version20250427203526	2025-04-27 20:43:40	13
DoctrineMigrations\\Version20250428053827	2025-04-28 05:38:52	9
DoctrineMigrations\\Version20250428055630	2025-04-28 05:56:46	26
DoctrineMigrations\\Version20250428073818	2025-04-28 07:38:30	1
DoctrineMigrations\\Version20250515142748	2025-05-15 14:31:25	4
DoctrineMigrations\\Version20250517213811	2025-05-17 21:39:17	8
DoctrineMigrations\\Version20250517221124	2025-05-17 22:11:43	1
DoctrineMigrations\\Version20250518053947	2025-05-18 05:40:51	3
\.


--
-- Data for Name: messenger_messages; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.messenger_messages (id, body, headers, queue_name, created_at, available_at, delivered_at) FROM stdin;
\.


--
-- Data for Name: user; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public."user" (id, email, roles, password, username, photo, role_type, preference) FROM stdin;
18	vanessa13@example.com	["ROLE_USER"]	$2y$13$Guh7TF5Rn2u3Xa.JoZJs5OzsS4s9WxrkMDwszdPwUbg2MpySOfa8C	Vanessa13	user1.png	["chauffeur_passager"]	\N
19	carla17@example.com	["ROLE_USER"]	$2y$13$CLV1.C0MFZCKultLWUiV1eEaj5SDE9xm4rw4fepCy2scv8uCnvOdq	Carla17	user2.png	["chauffeur_passager"]	\N
20	andre4125@example.com	["ROLE_USER"]	$2y$13$QYp5E2A5hSOXHbMsLwgJQ.a9nHdJhXChWnKjD8/h425jWCROFihhG	Andre4125	user3.png	["chauffeur_passager"]	\N
21	admin@ecoride.com	["ROLE_ADMIN"]	$2y$13$cWKUWvY/kM52c/jC0FAePu3eO0DPod50fJBqWLkQHUEbn/MuQ1hMq	Admin	\N	[]	\N
22	employee@ecoride.com	["ROLE_EMPLOYEE"]	$2y$13$DzGzbdyUiE5QrmkE2UqxaOLpjaMgVTWK4DEPHH6bh7QDP3EiGKhG.	Employé 1	\N	[]	\N
23	nom1@mail.com	["ROLE_USER"]	$2y$13$OkeVi7jc0nTaR.JIEa0kweJ7jUQxjAtUKA9t00MTesRTeb4c/6lZm	test1	\N	[]	\N
24	test2@mail.com	["ROLE_USER"]	$2y$13$TkqFJrte8MPuljdzeZw5SeQYjzah7plmpOZAnmpqcZl6OM1SCwSFm	test2	\N	[]	\N
\.


--
-- Name: car_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.car_id_seq', 15, true);


--
-- Name: carpooling_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.carpooling_id_seq', 15, true);


--
-- Name: credit_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.credit_id_seq', 16, true);


--
-- Name: messenger_messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.messenger_messages_id_seq', 1, false);


--
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.user_id_seq', 27, true);


--
-- Name: car car_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.car
    ADD CONSTRAINT car_pkey PRIMARY KEY (id);


--
-- Name: carpooling_passengers carpooling_passengers_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.carpooling_passengers
    ADD CONSTRAINT carpooling_passengers_pkey PRIMARY KEY (carpooling_id, user_id);


--
-- Name: carpooling carpooling_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.carpooling
    ADD CONSTRAINT carpooling_pkey PRIMARY KEY (id);


--
-- Name: credit credit_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.credit
    ADD CONSTRAINT credit_pkey PRIMARY KEY (id);


--
-- Name: doctrine_migration_versions doctrine_migration_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.doctrine_migration_versions
    ADD CONSTRAINT doctrine_migration_versions_pkey PRIMARY KEY (version);


--
-- Name: messenger_messages messenger_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.messenger_messages
    ADD CONSTRAINT messenger_messages_pkey PRIMARY KEY (id);


--
-- Name: user user_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);


--
-- Name: idx_1cc16efe67b3b43d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_1cc16efe67b3b43d ON public.credit USING btree (users_id);


--
-- Name: idx_6cc153f167b3b43d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_6cc153f167b3b43d ON public.carpooling USING btree (users_id);


--
-- Name: idx_6cc153f18702f506; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_6cc153f18702f506 ON public.carpooling USING btree (cars_id);


--
-- Name: idx_75ea56e016ba31db; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_75ea56e016ba31db ON public.messenger_messages USING btree (delivered_at);


--
-- Name: idx_75ea56e0e3bd61ce; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_75ea56e0e3bd61ce ON public.messenger_messages USING btree (available_at);


--
-- Name: idx_75ea56e0fb7336f0; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_75ea56e0fb7336f0 ON public.messenger_messages USING btree (queue_name);


--
-- Name: idx_773de69d67b3b43d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_773de69d67b3b43d ON public.car USING btree (users_id);


--
-- Name: idx_b28a59eaa76ed395; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_b28a59eaa76ed395 ON public.carpooling_passengers USING btree (user_id);


--
-- Name: idx_b28a59eaafb2200a; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_b28a59eaafb2200a ON public.carpooling_passengers USING btree (carpooling_id);


--
-- Name: uniq_identifier_email; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_identifier_email ON public."user" USING btree (email);


--
-- Name: messenger_messages notify_trigger; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON public.messenger_messages FOR EACH ROW EXECUTE FUNCTION public.notify_messenger_messages();


--
-- Name: credit fk_1cc16efe67b3b43d; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.credit
    ADD CONSTRAINT fk_1cc16efe67b3b43d FOREIGN KEY (users_id) REFERENCES public."user"(id);


--
-- Name: carpooling fk_6cc153f167b3b43d; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.carpooling
    ADD CONSTRAINT fk_6cc153f167b3b43d FOREIGN KEY (users_id) REFERENCES public."user"(id);


--
-- Name: carpooling fk_6cc153f18702f506; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.carpooling
    ADD CONSTRAINT fk_6cc153f18702f506 FOREIGN KEY (cars_id) REFERENCES public.car(id);


--
-- Name: car fk_773de69d67b3b43d; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.car
    ADD CONSTRAINT fk_773de69d67b3b43d FOREIGN KEY (users_id) REFERENCES public."user"(id);


--
-- Name: carpooling_passengers fk_b28a59eaa76ed395; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.carpooling_passengers
    ADD CONSTRAINT fk_b28a59eaa76ed395 FOREIGN KEY (user_id) REFERENCES public."user"(id) ON DELETE CASCADE;


--
-- Name: carpooling_passengers fk_b28a59eaafb2200a; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.carpooling_passengers
    ADD CONSTRAINT fk_b28a59eaafb2200a FOREIGN KEY (carpooling_id) REFERENCES public.carpooling(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict Y9TYYUmtPHco8wZNw0tsXuNPzrZgF0cetZ7b7aJa7KwqE1XorRIE4K8mdHvpaeC

